<?php

namespace App\Filament\Resources\HospitalAdmissions\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Forms\Components\RichEditor\RichContentRenderer;

class HospitalAdmissionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columnSpanFull()
                    ->columns(3)
                    ->compact()
                    ->schema([
                        TextEntry::make('hospital')
                            ->placeholder('-')
                            ->limit(35)
                            ->tooltip(fn($state) => $state),
                        TextEntry::make('admission_date')
                            ->date(),
                        TextEntry::make('discharge_date')
                            ->date()
                            ->placeholder('-'),
                    ]),
                Section::make()
                    ->columnSpanFull()
                    ->columns(2)
                    ->compact()
                    ->schema([
                        TextEntry::make('final_diagnosis')
                            ->placeholder('-')
                            ->formatStateUsing(fn($state) => static::renderToHtml($state))
                            ->html(),
                        TextEntry::make('remarks')
                            ->placeholder('-')
                            ->formatStateUsing(fn($state) => static::renderToHtml($state))
                            ->html(),
                    ])

            ]);
    }

    public static function renderToHtml($content): string
    {
        return RichContentRenderer::make($content)->toHtml() == '<p></p>' ? '' : RichContentRenderer::make($content)->toHtml();
    }
}
