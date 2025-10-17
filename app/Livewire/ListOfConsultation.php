<?php

namespace App\Livewire;

use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Schemas\Schema;
use App\Models\Patient;
use Filament\Forms\Get;
use Livewire\Component;
use Filament\Tables\Table;
use App\Models\Consultation;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use App\Filament\Resources\PatientResource;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Filament\Resources\ConsultationResource;
use Filament\Tables\Concerns\InteractsWithTable;

class ListOfConsultation extends Component implements HasTable, HasForms, HasActions
{

    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function table(Table $table): Table
    {

        // dd($this);
        return $table
            ->query(function () {
                return Consultation::query();
            })
            ->defaultSort('date', 'desc')
            ->columns([
                TextColumn::make('date')
                    ->label('consultation date')
                    ->dateTime('F j, Y'),
                TextColumn::make('patient.full_name')
                    ->label('Patient')
                    ->searchable(),
            ])
            ->filters([
                //
            ]);
    }

    public function form(Schema $schema): Schema
    {
        // return $form
        //     ->schema([
        //         DatePicker::make('date')
        //                 ->required(),
        //                 Select::make('patient_id')
        //                             ->label('Patient')
        //                             // ->getSearchResultsUsing(fn (string $search) => Patient::query()->where('full_name', 'like', "%$search%")->pluck('full_name', 'id'))
        //                             ->getOptionLabelsUsing(fn ($value) => Patient::find($value)->full_name)
        //                             ->preload()
        //                             ->searchable()
        //                             ->relationship('patient', 'full_name')
        //                             ->createOptionForm(function (Form $form) {
        //                                 return PatientResource::form($form)->extraAttributes(['class' => 'w-full']);
        //                             })
        //                             ->createOptionAction(function(Action $action) {
        //                                 return $action
        //                                     ->modalWidth('xl')
        //                                     ->modalHeading('Create Patient')
        //                                     ->mutateFormDataUsing(function(array $data) {
        //                                         $data['user_id'] = auth()->id();
        //                                         return $data;
        //                                     });
        //                             })
        //                             ->live()
        //                             ->required()
        //                             ->columnSpan(3)
        //     ])
        //     ->model(Consultation::class)
        //     ->statePath('data')
        //     ;
        return ConsultationResource::form($schema)
                ->model(Consultation::class)
                ->statePath('data');
    }

    public function render()
    {
        return view('livewire.list-of-consultation');
    }
}
