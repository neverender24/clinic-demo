<?php

namespace App\Livewire;

use App\Models\Patient;
use App\Models\ClinicSetting;
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
        $show = fn (string $key): bool => ClinicSetting::getConsultationSetting($key);

        $entries = [];

        if ($show('show_smoker_type')) {
            $entries[] = TextEntry::make('smoker_type')
                ->formatStateUsing(fn ($state) => match($state) {
                    'Smoker' => '🚬 Smoker',
                    'Non-smoker' => '🚭 Non-smoker',
                    'Vaper' => '💨 Vaper',
                    'Quitter' => '✅ Quitter',
                    default => $state,
                })
                ->color('danger');
        }

        $badgeEntries = [];

        if ($show('show_allergies')) {
            $badgeEntries[] = TextEntry::make('allergies')
                ->formatStateUsing(fn ($state) => "⚠️ {$state}")
                ->badge()
                ->color('danger');
        }

        if ($show('show_surgeries')) {
            $badgeEntries[] = TextEntry::make('surgeries')
                ->formatStateUsing(fn ($state) => "✂️ {$state}")
                ->badge()
                ->color('success')
                ->separator(',');
        }

        if (!empty($badgeEntries)) {
            $entries[] = Flex::make($badgeEntries)->columnSpanFull();
        }

        if ($show('show_medical_conditions')) {
            $entries[] = TextEntry::make('medical_conditions')
                ->formatStateUsing(fn ($state) => "❤️ {$state}")
                ->badge()
                ->color('success')
                ->columnSpanFull()
                ->separator(',');
        }

        if ($show('show_medications')) {
            $entries[] = TextEntry::make('medications')
                ->formatStateUsing(fn ($state) => "💊 {$state}")
                ->badge()
                ->color('success')
                ->columnSpanFull()
                ->separator(',');
        }

        if ($show('show_birthday')) {
            $entries[] = TextEntry::make('birthday')
                ->dateTime('F j, Y');
        }

        if ($show('show_age')) {
            $entries[] = TextEntry::make('birthday')
                ->label('Age')
                ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->age . ' years old' : null);
        }

        if ($show('show_sex')) {
            $entries[] = TextEntry::make('sex')
                ->formatStateUsing(fn ($state) => match($state) {
                    'M' => '👨 Male',
                    'F' => '👩 Female',
                    default => $state,
                });
        }

        if ($show('show_civil_status')) {
            $entries[] = TextEntry::make('civil_status')
                ->formatStateUsing(fn ($state) => match($state) {
                    'Child' => '💛 Child',
                    'Single' => '🙋 Single',
                    'Married' => '💍 Married',
                    'Widow' => '🖤 Widow',
                    'Widower' => '🖤 Widower',
                    default => $state,
                });
        }

        if ($show('show_address')) {
            $entries[] = TextEntry::make('address')
                ->formatStateUsing(fn ($state) => "📍 {$state}");
        }

        if ($show('show_occupation')) {
            $entries[] = TextEntry::make('occupation')
                ->formatStateUsing(fn ($state) => "🏠 {$state}");
        }

        return $schema
            ->record($this->patient)
            ->components([
                Section::make('👤 Details')
                    ->compact()
                    ->gap(false)
                    ->dense()
                    ->schema($entries)
                    ->columns(2),
            ]);
    }

    public function render()
    {
        return view('livewire.patient-detail');
    }
}
