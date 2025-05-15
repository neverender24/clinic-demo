<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\View\View;
use Filament\Tables\Table;
use App\Models\HospitalAdmission;
use Illuminate\Support\HtmlString;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Tables\Actions\Action;

class ListHospitalAdmission extends Component implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;
    // use InteractsWithRecord;

    public $patient_id;


    public function table(Table $table): Table
    {
        dump($this->patient_id);
        return $table
                ->query(fn() => HospitalAdmission::query()->where('patient_id', $this->patient_id))
                ->header(fn(): View => view('filament.hospital_admission.history-heading'))
                ->columns([
                    Split::make([
                        TextColumn::make('hospital'),
                        Stack::make([
                            TextColumn::make('admission_date')
                                ->dateTime('F j, Y')
                                ->icon('healthicons-o-admissions')
                                ->color('warning'),
                            TextColumn::make('discharge_date')
                                ->dateTime('F j, Y')
                                ->icon('healthicons-o-discharge')
                                ->color('success')
                        ])
                    ])
                ])
                ->actions([
                    Action::make('view')
                ])
                ->paginated(false);
    }

    public function render()
    {
        return view('livewire.list-hospital-admission');
    }
}
