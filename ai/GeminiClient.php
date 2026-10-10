<?php
// Thin client for Gemini chat (generateContent with tools, streamed).
// The key is read from the secrets file and never leaves the server.

class GeminiClient
{
    private $api_key;
    private $api_url;
    private $models;
    private $max_output_tokens;
    private $temperature;
    private $thinking_level;
    private $timeout;

    public function __construct(array $config, $api_key)
    {
        $this->api_key = (string) $api_key;
        $this->api_url = $config['chat_api_url'];
        $this->models = $config['chat_models'];
        $this->max_output_tokens = (int) $config['chat_max_output_tokens'];
        $this->temperature = (float) $config['chat_temperature'];
        $this->thinking_level = (string) ($config['chat_thinking_level'] ?? '');
        $this->timeout = (int) ($config['chat_timeout_seconds'] ?? 40);
    }

    public static function fromConfig(array $config)
    {
        return new self($config, secret($config['chat_secret'], ''));
    }

    public function isConfigured()
    {
        return $this->api_key !== '';
    }

    // One model turn. $contents is the conversation in Gemini's format; $on_text is called
    // with each piece of answer text as it arrives.
    // Returns ['parts' => [...], 'text' => string, 'function_calls' => [...], 'model' => string,
    //          'input_tokens' => int, 'output_tokens' => int, 'finish_reason' => string].
    // 'parts' must be stored and sent back unchanged on the next request: they carry the
    // thought signatures the model needs to continue after a tool call.
    public function generate($system_prompt, array $contents, array $function_declarations, callable $on_text = null)
    {
        if (!$this->isConfigured()) {
            throw new RuntimeException('Chat key is not set in the secrets file.');
        }

        $payload = [
            'systemInstruction' => ['parts' => [['text' => $system_prompt]]],
            'contents' => $contents,
            'generationConfig' => ['maxOutputTokens' => $this->max_output_tokens, 'temperature' => $this->temperature],
        ];
        if ($this->thinking_level !== '') {
            $payload['generationConfig']['thinkingConfig'] = ['thinkingLevel' => $this->thinking_level];
        }
        if ($function_declarations) {
            $payload['tools'] = [['functionDeclarations' => $function_declarations]];
        }
        $body = json_encode($payload, JSON_INVALID_UTF8_SUBSTITUTE);

        $last_error = 'no model configured';
        foreach ($this->models as $model) {
            $result = $this->stream($model, $body, $on_text);
            if ($result['ok']) {
                return $result;
            }
            $last_error = "$model: HTTP {$result['status']} {$result['error']}";
            // Only move to the next model when this one is busy, limited or silent (status 0 = no answer
            // in time), and nothing was shown yet.
            if (!in_array($result['status'], [0, 429, 500, 502, 503, 504], true) || $result['text'] !== '') {
                break;
            }
        }
        throw new RuntimeException('Chat request failed. ' . $last_error);
    }

    private function stream($model, $body, $on_text)
    {
        $result = [
            'ok' => false, 'status' => 0, 'error' => '', 'model' => $model, 'parts' => [], 'text' => '',
            'function_calls' => [], 'input_tokens' => 0, 'output_tokens' => 0, 'finish_reason' => '',
        ];
        $buffer = '';
        $raw = '';

        $handle_event = function ($json) use (&$result, $on_text) {
            $event = json_decode($json, true);
            if (!is_array($event)) {
                return;
            }
            if (isset($event['error'])) {
                $result['error'] = (string) ($event['error']['message'] ?? 'error');
                return;
            }
            $candidate = $event['candidates'][0] ?? [];
            foreach ($candidate['content']['parts'] ?? [] as $part) {
                if (!empty($part['thought'])) {
                    continue;
                }
                if (isset($part['functionCall'])) {
                    $result['function_calls'][] = $part['functionCall'];
                    $result['parts'][] = $part;
                    continue;
                }
                if (isset($part['text'])) {
                    $text = (string) $part['text'];
                    $last = count($result['parts']) - 1;
                    // Join streamed text pieces into one part; keep a signature that arrives with the last piece.
                    if ($last >= 0 && isset($result['parts'][$last]['text']) && !isset($result['parts'][$last]['functionCall'])) {
                        $result['parts'][$last]['text'] .= $text;
                        if (isset($part['thoughtSignature'])) {
                            $result['parts'][$last]['thoughtSignature'] = $part['thoughtSignature'];
                        }
                    } else {
                        $result['parts'][] = $part;
                    }
                    if ($text !== '') {
                        $result['text'] .= $text;
                        if ($on_text) {
                            $on_text($text);
                        }
                    }
                }
            }
            if (isset($candidate['finishReason'])) {
                $result['finish_reason'] = (string) $candidate['finishReason'];
            }
            if (isset($event['usageMetadata'])) {
                $usage = $event['usageMetadata'];
                $result['input_tokens'] = (int) ($usage['promptTokenCount'] ?? 0);
                // Thinking tokens are billed as output.
                $result['output_tokens'] = (int) ($usage['candidatesTokenCount'] ?? 0) + (int) ($usage['thoughtsTokenCount'] ?? 0);
            }
        };

        $curl = curl_init($this->api_url . rawurlencode($model) . ':streamGenerateContent?alt=sse');
        curl_setopt_array($curl, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => ['x-goog-api-key: ' . $this->api_key, 'Content-Type: application/json'],
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_WRITEFUNCTION => function ($curl, $chunk) use (&$buffer, &$raw, $handle_event) {
                $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
                if ($status !== 200) {
                    // An error answer is plain JSON, not a stream: collect it whole.
                    $raw .= $chunk;
                    return strlen($chunk);
                }
                $buffer .= $chunk;
                while (($end = strpos($buffer, "\n")) !== false) {
                    $line = rtrim(substr($buffer, 0, $end), "\r");
                    $buffer = substr($buffer, $end + 1);
                    if (strncmp($line, 'data:', 5) === 0) {
                        $handle_event(ltrim(substr($line, 5)));
                    }
                }
                return strlen($chunk);
            },
        ]);
        $done = curl_exec($curl);
        $result['status'] = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($curl);
        curl_close($curl);

        if ($done === false) {
            $result['error'] = $curl_error;
            return $result;
        }
        if ($result['status'] !== 200) {
            $decoded = json_decode($raw, true);
            $result['error'] = is_array($decoded) ? (string) ($decoded['error']['message'] ?? '') : '';
            return $result;
        }
        if (strncmp(ltrim($buffer), 'data:', 5) === 0) {
            $handle_event(ltrim(substr(ltrim($buffer), 5)));
        }
        if ($result['error'] !== '') {
            return $result;
        }

        $result['ok'] = true;
        return $result;
    }
}
