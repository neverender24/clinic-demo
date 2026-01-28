<?php

namespace App\Filament\Resources\Consultations\Schemas;

use App\Trait\HasTotal;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;
use Filament\Support\Enums\TextSize;
use App\Trait\HasRichContentRenderer;
use Filament\Schemas\Components\Tabs;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;

class ConsultationInfolist
{

    use HasTotal;
    use HasRichContentRenderer;

    public static function configure(Schema $schema): Schema
    {
        return $schema

            ->components([
                        Tabs::make('tabs')
                        ->dense()
                        ->gap(false)
                        ->extraAttributes([
                            'class' => 'patient-history'
                        ])
                        ->vertical()
                            ->tabs([
                                Tab::make('Consultation')
                                    ->dense()
                                    ->gap(false)
                                    ->schema([
                                        Section::make()
                                            ->columns(1)
                                            ->schema([
                                                Section::make()
                                                    ->schema([
                                                        TextEntry::make('chief_complaint')
                                                            ->label('Subjective')
                                                            ->formatStateUsing(fn ($state) => static::renderToHtml($state))
                                                            ->html(),
                                                    ]),
                                                Section::make()
                                                    ->schema([
                                                        TextEntry::make('test_results')
                                                            ->label('Objective')
                                                            ->formatStateUsing(fn ($state) => static::renderToHtml($state))
                                                            ->html(),
                                                    ])
                                                    ->hiddenLabel(false),
                                                Section::make()
                                                    ->schema([
                                                        TextEntry::make('diagnosis')
                                                            ->label('Assessment')
                                                            ->formatStateUsing(fn ($state) => static::renderToHtml($state))
                                                            ->html(),
                                                    ])
                                                    ->hiddenLabel(false),
                                                Section::make()
                                                    ->schema([
                                                        TextEntry::make('management')
                                                            ->label('Plan')
                                                            ->formatStateUsing(fn ($state) => static::renderToHtml($state))
                                                            ->html(),
                                                    ])
                                                    ->hiddenLabel(false),
                                            ]),
                                    ]),
                                Tab::make('Prescription')
                                    ->badge(fn($record) => $record->medicines->count())
                                    ->dense()
                                    ->gap(false)
                                    ->schema([
                                        RepeatableEntry::make('medicines')
                                        ->hiddenLabel()
                                        ->grid(2)
                                        ->dense()
                                        ->gap(false)
                                        ->table([
                                            TableColumn::make('Medicine'),
                                            TableColumn::make('Remarks'),
                                            TableColumn::make('Qty')
                                            ->width('10%'),
                                        ])
                                        ->schema([
                                            TextEntry::make('name')
                                                ->formatStateUsing(function ($state, $record) {
                                                    return "$state ({$record->brand})";
                                                })
                                                ->hiddenLabel()
                                                ->html(),
                                            TextEntry::make('pivot.remarks')
                                                ->hiddenLabel(),
                                            TextEntry::make('pivot.quantity')
                                                ->formatStateUsing(fn($state) => '#' . $state)
                                                ->hiddenLabel()
                                        ])
                                    ]),
                                
                                Tab::make('Other Attachments')
                                    ->dense()
                                    ->gap(false)
                                    ->schema([
                                        ImageEntry::make('attachments')
                                            ->imageWidth('50%')
                                            ->imageHeight('50%')
                                    ])
                                ]),
                       
                    ]);
    }

    public static function getChargeSlipSchema(): Section
    {
        return 
            Section::make('Charge Slip')
                ->columns(2)
                ->schema([
                    TextEntry::make('fee')
                        ->inlineLabel(),
                    TextEntry::make('follow_up_fees')
                        ->inlineLabel(),
                    // TextInput::make('procedure')
                    //     ->inlineLabel(),
                    TextEntry::make('procedure_fee')
                        ->inlineLabel(),
                    TextEntry::make('discount')
                        ->inlineLabel()
                        ->label('Discount(%)'),
                    TextEntry::make('Total')
                        ->inlineLabel()
                        ->default(function($record) {
                            $total = static::getTotal($record->fee, $record->follow_up_fees, $record->procedure_fee, $record->discount);

                            return $total;

                        })
                        ->numeric(decimalPlaces: 2)
                        ->label('Total')
                ]);
    }

    protected static function onlyAllowedToolbar(): array
    {
        return [
            'bold',
            'bulletList',
            'italic',
            'orderedList',
            'redo',
            'undo',
            'attachFiles'
        ];
    }
}
