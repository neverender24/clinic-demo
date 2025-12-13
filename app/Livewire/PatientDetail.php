<?php

namespace App\Livewire;

use App\Models\Patient;
use Livewire\Component;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Livewire\Attributes\Reactive;

class PatientDetail extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    #[Reactive]
    public $patient_id;

    public function detailForm(Schema $schema): Schema
    {
        // if ($this->patient_id) {
        //     dd($this->patient_id);
        // }
         return $schema
                ->record(Patient::where('id', $this->patient_id)->first())
                ->components([
                    Section::make('Details')
                    ->schema([
                        TextEntry::make('allergies')->badge()
                            ->color('danger')
                            ->separator(','),
                        TextEntry::make('surgeries')->badge()
                            ->color('success')
                            ->separator(','),
                        TextEntry::make('birthday')
                            ->date(format: 'F j, Y'),
                        TextEntry::make('sex'),
                        TextEntry::make('civil_status'),
                        TextEntry::make('address'),
                    ])->columns(2)
                ]);
    }

    public function render()
    {
        return view('livewire.patient-detail');
    }
}
