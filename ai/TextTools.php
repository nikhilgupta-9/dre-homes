<?php
// Text helpers for the index: HTML to plain text, and splitting long text into chunks.

class TextTools
{
    // Admin-written HTML -> plain text with paragraph breaks kept as blank lines.
    public static function htmlToText($html)
    {
        $text = (string) $html;
        $text = preg_replace('/<(script|style|noscript|iframe|form|select|svg)\b[^>]*>.*?<\/\1>/is', ' ', $text);
        $text = preg_replace('/<!--.*?-->/s', ' ', $text);
        $text = preg_replace('/<\s*br\s*\/?>/i', "\n", $text);
        $text = preg_replace('/<\/\s*(p|div|h[1-6]|li|tr|ul|ol|table|section|article|blockquote)\s*>/i', "\n\n", $text);
        $text = preg_replace('/<\s*li\b[^>]*>/i', '- ', $text);
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = str_replace(["\xC2\xA0", "\r"], [' ', ''], $text);
        $text = preg_replace('/[ \t]+/', ' ', $text);
        $text = preg_replace('/ *\n */', "\n", $text);
        // A list marker left alone on its line joins the text that follows it.
        $text = preg_replace('/^-\n+(?=\S)/m', '- ', $text);
        $text = preg_replace('/\n{3,}/', "\n\n", $text);
        return trim($text);
    }

    // About four characters per token. Good enough for limits and cost estimates.
    public static function estimateTokens($text)
    {
        return (int) ceil(mb_strlen((string) $text) / 4);
    }

    // Splits text into pieces of at most $max_chars, cutting at paragraph ends where
    // possible, then at sentence ends, and only as a last resort in the middle of a sentence.
    // A final piece shorter than $min_chars is joined to the one before it.
    public static function chunk($text, $max_chars = 1600, $min_chars = 200)
    {
        $text = trim((string) $text);
        if ($text === '') {
            return [];
        }
        if (mb_strlen($text) <= $max_chars) {
            return [$text];
        }

        $pieces = [];
        foreach (preg_split('/\n{2,}/', $text) as $paragraph) {
            $paragraph = trim($paragraph);
            if ($paragraph === '') {
                continue;
            }
            if (mb_strlen($paragraph) <= $max_chars) {
                $pieces[] = $paragraph;
                continue;
            }
            foreach (preg_split('/(?<=[.!?])\s+/u', $paragraph) as $sentence) {
                while (mb_strlen($sentence) > $max_chars) {
                    $pieces[] = mb_substr($sentence, 0, $max_chars);
                    $sentence = mb_substr($sentence, $max_chars);
                }
                if ($sentence !== '') {
                    $pieces[] = $sentence;
                }
            }
        }

        $chunks = [];
        $current = '';
        foreach ($pieces as $piece) {
            if ($current === '') {
                $current = $piece;
            } elseif (mb_strlen($current) + 2 + mb_strlen($piece) <= $max_chars) {
                $current .= "\n\n" . $piece;
            } else {
                $chunks[] = $current;
                $current = $piece;
            }
        }
        if ($current !== '') {
            $last = count($chunks) - 1;
            if ($last >= 0 && mb_strlen($current) < $min_chars && mb_strlen($chunks[$last]) + 2 + mb_strlen($current) <= $max_chars * 1.25) {
                $chunks[$last] .= "\n\n" . $current;
            } else {
                $chunks[] = $current;
            }
        }
        return $chunks;
    }
}
