<?php

namespace App\Livewire\Consultation;

use Livewire\Component;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Consultation;
use Filament\Tables\Actions\Action;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Filament\Resources\ConsultationResource;
use Filament\Tables\Concerns\InteractsWithTable;

class ListRecords extends Component  implements HasForms, HasTable
{

    use InteractsWithTable;
    use InteractsWithForms;

    public $patient_id;

    public ?array $data = [];

    public function mount(Consultation $consultation): void
    {
        dd($this->patient_id);
        $this->form->fill();
    }
    

    // public function form(Form $form): Form
    // {
        
    //     return ConsultationResource::form($form)
    //         ->columns(1)
    //         ->model(Consultation::class)
    //         ->statePath('data');
    // }
    
    public function table(Table $table): Table
    {   
        return $table
                ->query(fn() => Consultation::query()->where('patient_id', $this->patient_id))
                ->columns([
                    TextColumn::make('date')
                        ->label('Date of Consultation')
                        ->dateTime('F j, Y')
                ])
                ->actions([
                    Action::make('select')
                        ->action('selectHistory')
                ])
                ->actionsColumnLabel('Action')
                ->heading('Previous Consultations');
    }

    public function selectHistory()
    {
        $this->dispatch('selected-history');
    }

    public function render()
    {
        return view('livewire.consultation.list-records');
    }
    
}
