<?php

namespace App\Trait;

use Filament\Forms\Components\RichEditor\RichContentRenderer;

trait HasRichContentRenderer
{
    public static function renderToHtml($content): string
    {
        if (empty($content)) {
            return '';
        }

        $html = RichContentRenderer::make($content)->toHtml();

        return $html === '<p></p>' ? '' : $html;
    }

}
