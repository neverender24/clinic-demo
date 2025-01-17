<?php

namespace App\Filament\Resources\ConsultationResource\Pages;

use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Consultation;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\Action;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Filament\Resources\ConsultationResource;
use Filament\Tables\Concerns\InteractsWithTable;

class EditWithHistory extends Page implements HasForms, HasTable
{
    protected static string $resource = ConsultationResource::class;

    protected static string $view = 'filament.resources.consultation-resource.pages.edit-with-history';

    use InteractsWithTable;
    use InteractsWithForms;

    public $patient_id;

    public Consultation $consultation;

    public $data;

    public function mount($record)
    {
        $this->consultation = Consultation::findOrFail($record)->load('consultationMedicines');

        $this->patient_id = $this->consultation->patient_id;

        $this->consultation->medicines = $this->consultation->consultationMedicines->map(fn($item) => [
                                            "consultation_id" => $item->consultation_id,
                                            "medicine_id" => $item->medicine_id,
                                            "remarks" => $item->remarks
                                        ]);
    
        $this->form->fill(collect($this->consultation)->except('consultation_medicines')->toArray());
        
    }

    public function form(Form $form): Form
    {
        return ConsultationResource::form($form)
            ->columns(1)
            ->statePath('data')
            ->model(Consultation::class);
    }
    
    public function table(Table $table): Table
    {   
        return $table
                ->query(fn() => Consultation::query()
                                ->where('patient_id', $this->patient_id)
                                ->whereDate('date', '<', $this->data['date'])
                )
                ->columns([
                    TextColumn::make('date')
                        ->label('Date of Consultation')
                        ->dateTime('F j, Y')
                ])
                ->actions([
                    Action::make('select')
                        ->action(fn($record) => $this->selectHistory($record))
                ])
                ->actionsColumnLabel('Action')
                ->heading('Previous Consultations');
    }

    protected function selectHistory($record)
    {
        // $record = collect($record)->except('date');
        // dd($record);
        $record->date = $this->consultation->date; 

        $record->id = $this->consultation->id; 

        // dd($this->consultation);
        
        $this->data = $record->toArray();

        // dd($this->data);
        
    }

    public function submit()
    {
        $this->validate();

        DB::transaction(function() {
            try {
                $this->consultation->update($this->data);

                $this->consultation->medicines()->attach($this->data['medicines']);

                Notification::make()
                    ->title('Saved successfully')
                    ->success()
                    ->send();

            } catch (\Throwable $th) {
                Notification::make()
                    ->title('Error')
                    ->body($th->getMessage())
                    ->danger()
                    ->send();
            }
        });
    }

    public function cancelAction()
    {
        
    }
}
