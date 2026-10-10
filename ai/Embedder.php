<?php
// Calls the embeddings provider (Google Gemini or Voyage AI, chosen in ai/config.php).
// The key is read from the secrets file and never leaves the server.
// Only public listing and article text is sent here.

class Embedder
{
    private $provider;
    private $api_key;
    private $api_url;
    private $model;
    private $secret_name;
    private $dimension;
    private $tokens_used = 0;

    public function __construct(array $config, $api_key)
    {
        $this->provider = $config['embedding_provider'];
        if (!isset($config['embedding_providers'][$this->provider])) {
            throw new RuntimeException("Unknown embedding provider '{$this->provider}' in ai/config.php.");
        }
        $settings = $config['embedding_providers'][$this->provider];

        $this->api_key = (string) $api_key;
        $this->api_url = $settings['api_url'];
        $this->model = $settings['model'];
        $this->secret_name = $settings['secret'];
        $this->dimension = (int) $config['embedding_dim'];
    }

    // Builds the embedder with the key of the chosen provider from the secrets file.
    public static function fromConfig(array $config)
    {
        $settings = $config['embedding_providers'][$config['embedding_provider']] ?? null;
        return new self($config, $settings ? secret($settings['secret'], '') : '');
    }

    public function isConfigured()
    {
        return $this->api_key !== '';
    }

    public function secretName()
    {
        return $this->secret_name;
    }

    public function model()
    {
        return $this->model;
    }

    public function dimension()
    {
        return $this->dimension;
    }

    // Tokens sent to the provider. Gemini does not report them for embeddings,
    // so for Gemini this is our own estimate (about four characters per token).
    public function tokensUsed()
    {
        return $this->tokens_used;
    }

    // Texts being stored in the index.
    public function embedDocuments(array $texts)
    {
        return $this->embed(array_values($texts), 'document');
    }

    // A visitor's search text.
    public function embedQuery($text)
    {
        $vectors = $this->embed([(string) $text], 'query');
        return $vectors[0];
    }

    // Returns one list of floats per input text, in the same order. Throws on any failure.
    private function embed(array $texts, $input_type)
    {
        if (!$this->isConfigured()) {
            throw new RuntimeException("Embeddings key is not set ({$this->secret_name} in the secrets file).");
        }
        if (empty($texts)) {
            return [];
        }

        $vectors = $this->provider === 'gemini'
            ? $this->embedWithGemini($texts, $input_type)
            : $this->embedWithVoyage($texts, $input_type);

        if (count($vectors) !== count($texts)) {
            throw new RuntimeException('Embeddings answer has ' . count($vectors) . ' vectors for ' . count($texts) . ' texts.');
        }
        foreach ($vectors as $vector) {
            if (!is_array($vector) || count($vector) !== $this->dimension) {
                throw new RuntimeException('Embeddings answer has a vector of the wrong size.');
            }
        }
        return $vectors;
    }

    private function embedWithGemini(array $texts, $input_type)
    {
        $task_type = $input_type === 'query' ? 'RETRIEVAL_QUERY' : 'RETRIEVAL_DOCUMENT';
        $requests = [];
        foreach ($texts as $text) {
            $requests[] = [
                'model' => 'models/' . $this->model,
                'content' => ['parts' => [['text' => $text]]],
                'taskType' => $task_type,
                'outputDimensionality' => $this->dimension,
            ];
        }

        $data = $this->post(
            $this->api_url . rawurlencode($this->model) . ':batchEmbedContents',
            ['x-goog-api-key: ' . $this->api_key],
            ['requests' => $requests]
        );
        if (!isset($data['embeddings']) || !is_array($data['embeddings'])) {
            throw new RuntimeException('Embeddings answer has no embeddings.');
        }

        foreach ($texts as $text) {
            $this->tokens_used += (int) ceil(mb_strlen($text) / 4);
        }
        // Vectors shorter than the model's full size are brought to length 1 when stored (VectorStore::pack).
        return array_map(function ($item) {
            return $item['values'] ?? null;
        }, $data['embeddings']);
    }

    private function embedWithVoyage(array $texts, $input_type)
    {
        $data = $this->post(
            $this->api_url,
            ['Authorization: Bearer ' . $this->api_key],
            [
                'input' => $texts,
                'model' => $this->model,
                'input_type' => $input_type,
                'output_dimension' => $this->dimension,
                'truncation' => true,
            ]
        );
        if (!isset($data['data']) || !is_array($data['data'])) {
            throw new RuntimeException('Embeddings answer has no data.');
        }

        $this->tokens_used += (int) ($data['usage']['total_tokens'] ?? 0);

        $vectors = [];
        foreach ($data['data'] as $position => $item) {
            $vectors[isset($item['index']) ? (int) $item['index'] : $position] = $item['embedding'] ?? null;
        }
        ksort($vectors);
        return array_values($vectors);
    }

    // POSTs JSON and returns the decoded answer. Rate limits and short outages are retried with a growing pause.
    private function post($url, array $headers, array $payload)
    {
        $body = json_encode($payload, JSON_INVALID_UTF8_SUBSTITUTE);
        $headers[] = 'Content-Type: application/json';

        $response = false;
        $status = 0;
        $error = '';
        for ($attempt = 1; $attempt <= 4; $attempt++) {
            $curl = curl_init($url);
            curl_setopt_array($curl, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $body,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_TIMEOUT => 60,
            ]);
            $response = curl_exec($curl);
            $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $error = curl_error($curl);
            curl_close($curl);

            $retry = $response === false || $status === 429 || $status >= 500;
            if (!$retry || $attempt === 4) {
                break;
            }
            sleep($attempt * 5);
        }

        if ($response === false) {
            throw new RuntimeException('Embeddings request failed: ' . $error);
        }
        $data = json_decode($response, true);
        if ($status !== 200 || !is_array($data)) {
            $detail = is_array($data) ? (string) ($data['error']['message'] ?? ($data['detail'] ?? '')) : '';
            throw new RuntimeException("Embeddings request failed with HTTP $status" . ($detail !== '' ? ": $detail" : ''));
        }
        return $data;
    }
}
