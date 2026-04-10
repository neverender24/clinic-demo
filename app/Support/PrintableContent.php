<?php

namespace App\Support;

class PrintableContent
{
    public static function toHtml(mixed $content): string
    {
        if (blank($content)) {
            return '';
        }

        $content = self::normalizeEntities((string) $content);

        if ($content !== strip_tags($content)) {
            return $content;
        }

        return nl2br(e($content));
    }

    public static function toPlainText(mixed $content): string
    {
        if (blank($content)) {
            return '';
        }

        $content = self::normalizeEntities((string) $content);

        if ($content === strip_tags($content)) {
            return trim(preg_replace("/\R{3,}/", "\n\n", $content) ?? $content);
        }

        $blockBreaks = [
            '/<\s*br\s*\/?>/i' => "\n",
            '/<\s*\/p\s*>/i' => "\n\n",
            '/<\s*\/div\s*>/i' => "\n",
            '/<\s*\/h[1-6]\s*>/i' => "\n",
            '/<\s*li\b[^>]*>/i' => '- ',
            '/<\s*\/li\s*>/i' => "\n",
        ];

        $content = preg_replace(array_keys($blockBreaks), array_values($blockBreaks), $content) ?? $content;
        $content = strip_tags($content);
        $content = preg_replace("/[ \t]+\n/", "\n", $content) ?? $content;
        $content = preg_replace("/\n{3,}/", "\n\n", $content) ?? $content;

        return trim($content);
    }

    protected static function normalizeEntities(string $content): string
    {
        $decoded = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return str_replace("\u{00A0}", ' ', $decoded);
    }
}
