<?php

namespace App\Livewire;

use App\Filament\Resources\HospitalAdmissions\Schemas\HospitalAdmissionInfolist;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Action;
use Livewire\Component;
use Illuminate\View\View;
use Filament\Tables\Table;
use App\Models\HospitalAdmission;
use Filament\Actions\ViewAction;
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
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Livewire\Attributes\Reactive;

class ListHospitalAdmission extends Component implements HasTable, HasSchemas, HasActions
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas    ;
    // use InteractsWithRecord;

    #[Reactive]
    public $patient_id;


    public function table(Table $table): Table
    {
        return $table
                ->query(fn() => HospitalAdmission::query()->where('patient_id', $this->patient_id))
                ->header(fn(): View => view('filament.hospital_admission.history-heading'))
                ->columns([
                    Split::make([
                        TextColumn::make('hospital')
                            ->limit(20)
                            ->tooltip(fn($state) => $state),
                        Stack::make([
                            TextColumn::make('admission_date')
                                ->dateTime('m-d-Y')
                                // ->icon('healthicons-o-admissions')
                                ->color('warning'),
                            TextColumn::make('discharge_date')
                                ->dateTime('m-d-Y')
                                // ->icon('healthicons-o-discharge')
                                ->color('success')
                        ])
                    ])
                ])
                ->recordActions([
                    ViewAction::make('view')
                        ->schema(fn(Schema $schema) => HospitalAdmissionInfolist::configure($schema)->columns(2))
                        ->modalHeading(fn($record) => $record->patient?->full_name)
                ])
                ->paginated(false);
    }

    public function render()
    {
        return view('livewire.list-hospital-admission');
    }
}
