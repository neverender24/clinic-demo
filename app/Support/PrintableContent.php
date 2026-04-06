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

        return trim(strip_tags(self::normalizeEntities((string) $content)));
    }

    protected static function normalizeEntities(string $content): string
    {
        $decoded = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return str_replace("\u{00A0}", ' ', $decoded);
    }
}
