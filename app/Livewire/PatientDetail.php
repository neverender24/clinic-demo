<?php

namespace App\Livewire;

use App\Models\Patient;
use Livewire\Component;
use Filament\Schemas\Schema;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Reactive;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Concerns\InteractsWithSchemas;

class PatientDetail extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    #[Reactive]
    public $patient_id;

    #[Computed]
    public function patient(): ?Patient
    {
        return $this->patient_id
            ? Patient::find($this->patient_id)
            : null;
    }

    public function detailForm(Schema $schema): Schema
    {
        return $schema
            ->record($this->patient)
            ->components([
                Section::make('👤 Details')
                    ->compact()
                    ->gap(false)
                    ->dense()
                    ->schema([
                        TextEntry::make('smoker_type')
                            ->formatStateUsing(fn ($state) => match($state) {
                                'Smoker' => '🚬 Smoker',
                                'Non-smoker' => '🚭 Non-smoker',
                                'Vaper' => '💨 Vaper',
                                'Quitter' => '✅ Quitter',
                                default => $state,
                            })
                            ->color('danger'),
                        Flex::make([
                            TextEntry::make('allergies')
                                ->formatStateUsing(fn ($state) => "⚠️ {$state}")
                                ->badge()
                                ->color('danger'),
                            TextEntry::make('surgeries')
                                ->formatStateUsing(fn ($state) => "✂️ {$state}")
                                ->badge()
                                ->color('success')
                                ->separator(','),
                        ])
                        ->columnSpanFull(),
                        TextEntry::make('medical_conditions')
                            ->formatStateUsing(fn ($state) => "❤️ {$state}")
                            ->badge()
                            ->color('success')
                            ->columnSpanFull()
                            ->separator(','),
                        TextEntry::make('medications')
                            ->formatStateUsing(fn ($state) => "💊 {$state}")
                            ->badge()
                            ->color('success')
                            ->columnSpanFull()
                            ->separator(','),
                        TextEntry::make('birthday')
                            ->dateTime('F j, Y')
                            // ->formatStateUsing(fn ($state) => "🎂 " . $state?->format('F j, Y'))
                            ,
                        TextEntry::make('birthday')
                            ->label('Age')
                            ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->age . ' years old' : null),
                        TextEntry::make('sex')
                            ->formatStateUsing(fn ($state) => match($state) {
                                'M' => '👨 Male',
                                'F' => '👩 Female',
                                default => $state,
                            }),
                        TextEntry::make('civil_status')
                            ->formatStateUsing(fn ($state) => match($state) {
                                'Child' => '💛 Child',
                                'Single' => '🙋 Single',
                                'Married' => '💍 Married',
                                'Widow' => '🖤 Widow',
                                'Widower' => '🖤 Widower',
                                default => $state,
                            }),
                        TextEntry::make('address')
                            ->formatStateUsing(fn ($state) => "📍 {$state}"),
                        TextEntry::make('occupation')
                            ->formatStateUsing(fn ($state) => "🏠 {$state}"),
                    ])
                    ->columns(2),
            ]);
    }

    public function render()
    {
        return view('livewire.patient-detail');
    }
}
