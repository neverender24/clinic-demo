<?php

namespace App\Filament\Resources\ConsultationResource\Pages;

use Carbon\Carbon;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Consultation;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\Action;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use App\Infolists\Components\PatientEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Filament\Resources\ConsultationResource;
use Filament\Tables\Concerns\InteractsWithTable;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;

class EditWithHistory extends Page implements HasForms, HasTable
{
    protected static string $resource = ConsultationResource::class;

    protected static string $view = 'filament.resources.consultation-resource.pages.edit-with-history';

    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithRecord;
    // use HasPageShield;

    public $patient_id;

    public Consultation $consultation;

    public $data;

    protected static function getPermissionName(): string
    {
        return 'edit_as_doctor';
    }


    public function mount(int | string $record): void
    {
        
        $this->record = $this->resolveRecord($record)->load('consultationMedicines');

        // dd(auth()->user());

        $this->authorize('edit_as_doctor_consultation', [$this->record]);
        
        $this->patient_id = $this->record->patient_id;

        $this->record->medicines = $this->record->consultationMedicines->map(fn($item) => [
                                            "consultation_id" => $item->consultation_id,
                                            "medicine_id" => $item->medicine_id,
                                            "remarks" => $item->remarks
                                        ]);
    
        $this->form->fill(collect($this->record)->except('consultation_medicines')->toArray());
        
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
                        ->modal()
                        ->infolist([
                            Section::make()
                                ->schema([
                                    PatientEntry::make('patient.full_name')
                                        ->inlineLabel(false)
                                        ->hiddenLabel()
                                        ->size(TextEntry\TextEntrySize::Large)
                                        ->icon('healthicons-o-traumatism')
                                        ->iconColor('white')
                                        ->formatStateUsing(fn($state) => strtoupper($state)),
                                    TextEntry::make('test_results')
                                        ->html()
                                ])
                        ])
                        ->modalHeading(fn($record) => 'Consultation Details '.Carbon::parse($record->date)->format('F j, Y'))
                        // ->action(fn($record) => $this->selectHistory($record))
                ])
                ->actionsColumnLabel('Action')
                ->heading('Previous Consultations')
                ->paginated(false);
    }

    protected function selectHistory($record)
    {
        // $record = collect($record)->except('date');
        // dd($record);
        $record->date = $this->record->date; 

        $record->id = $this->record->id; 

        // dd($this->record);
        
        $this->data = $record->toArray();

        // dd($this->data);
        
    }

    public function submit()
    {
        $this->validate();

        DB::transaction(function() {
            try {
                $this->record->update($this->data);

                $this->record->medicines()->attach($this->data['medicines']);

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
