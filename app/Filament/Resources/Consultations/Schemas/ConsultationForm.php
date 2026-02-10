<?php

namespace App\Filament\Resources\Consultations\Schemas;

use App\Models\User;
use App\Models\Patient;
use App\Models\Medicine;
use Illuminate\View\View;
use App\Models\DrawFinding;
use Filament\Support\RawJs;
use App\Models\Consultation;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use App\Models\HospitalAdmission;
use Illuminate\Support\HtmlString;
use App\Models\ConsultationMedicine;
use Filament\Support\Enums\IconSize;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Text;
use App\Forms\Components\HistoryField;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Utilities\Set;
use App\Filament\Forms\Components\PatientField;
use App\Filament\Forms\Components\PatientDetail;
use App\Filament\Forms\Components\PatientHistory;
use Filament\Forms\Components\Repeater\TableColumn;
use App\Filament\Resources\Patients\PatientResource;
use App\Filament\Resources\Medicines\MedicineResource;
use App\Filament\Resources\Patients\Schemas\PatientForm;
use Filament\Schemas\Components\View as ComponentsView;

class ConsultationForm
{
    public static function configure(Schema $schema): Schema
    {
        
        return $schema
            ->components([
                Grid::make(3)
                    ->schema([
                        Section::make()
                            ->columnSpan(2)
                            ->columns([
                                'default' => 1,
                                'sm' => 2
                            ])
                            ->compact()
                            ->dense()
                            ->schema([
                                DatePicker::make('date')
                                    ->default(now())
                                    ->required()
                                    ->native(false),
                                Select::make('doctor_id')
                                    ->label('Assigned Doctor')
                                    ->options(function () {
                                        return User::role('Doctor')->pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->preload(),
                                \Filament\Forms\Components\Toggle::make('is_dialysis')
                                    ->label('Dialysis')
                                    ->inline(false),
                                Select::make('patient_id')
                                    ->label('Patient')
                                    ->relationship('patient', 'full_name')
                                    // ->getSearchResultsUsing(fn (string $search) => Patient::query()->where('full_name', 'like', "%$search%")->pluck('full_name', 'id'))
                                    ->getOptionLabelsUsing(fn($value) => Patient::find($value)->full_name)
                                    ->preload()
                                    ->searchable()
                                    ->createOptionForm(fn(Schema $schema) => PatientForm::configure($schema)->columns(1)->extraAttributes(['class' => 'w-full']))
                                    ->createOptionAction(function (Action $action) {
                                        return $action
                                            ->modalWidth('xl')
                                            ->modalHeading('Create Patient')
                                            ->mutateDataUsing(function (array $data) {
                                                $data['user_id'] = auth()->id();
                                                return $data;
                                            });
                                    })
                                    ->editOptionForm(fn(Schema $schema) => PatientForm::configure($schema)->columns(1)->extraAttributes(['class' => 'w-full']))
                                    ->editOptionAction(function (Action $action, $livewire) {
                                        return $action
                                            ->modalWidth('xl')
                                            ->modalHeading(fn($data) => 'Edit ' . Patient::findOrFail($livewire->data['patient_id'])?->full_name)
                                            ->mutateDataUsing(function (array $data) {
                                                $data['user_id'] = auth()->id();
                                                return $data;
                                            });
                                    })
                                    ->live()
                                    ->required(),
                                Textarea::make('chief_complaint')
                                    ->label('Subjective')
                                    ->columnSpanFull()
                                    ->autosize()
                                    ->required()
                                    ->hint(fn(): View => view('forms.components.vital-signs-hint'))
                                    ->afterStateHydrated(fn(Set $set, $state) => $set('chief_complaint', strip_tags($state)))
                                    ->visible(fn() => auth()->user()->can('addChiefComplaint', Consultation::class)),
                                // Textarea::make('vital_signs')
                                //     ->columnSpan(fn() => [
                                //         'sm' => auth()->user()->can('addVitalSign', Consultation::class) ? 1 : 2
                                //     ])
                                //     ->autosize()
                                //     ->required()
                                //     ->default(function () {
                                //         $words = ['BP: ', 'HR: ', 'Weight: '];
                                //         return implode("\n", $words); // line break per word
                                //     })
                                //     ->afterStateHydrated(function (Set $set, $state) {
                                //         if (!$state) {
                                //             # code...
                                //             $words = ['BP: ', 'HR: ', 'Weight: '];
                                //             $set('vital_signs', implode("\n", $words)); // line break per word
                                //         }
                                //     })
                                //     ->visible(fn() => auth()->user()->can('addVitalSign', Consultation::class)),

                                Textarea::make('test_results')
                                    // ->columnSpan(fn() => [
                                    //     'sm' => auth()->user()->can('addVitalSign', Consultation::class) ? 1 : 2
                                    // ])
                                    ->columnSpanFull()
                                    ->label('Objective')
                                    ->autosize()
                                    // ->dehydrateStateUsing(fn($state) => strip_tags($state))
                                    ->afterStateHydrated(fn(Set $set, $state) => $set('test_results', strip_tags($state)))
                                    ->required()
                                    ->visible(fn() => auth()->user()->can('addTestResult', Consultation::class)),
                                Textarea::make('diagnosis')
                                    ->label('Assessment')
                                    ->columnSpanFull()
                                    ->autosize()
                                    ->required()
                                    // ->toolbarButtons(self::onlyAllowedToolbar())
                                    // ->hint(fn($operation): View | null => $operation == 'create' ? null : view('forms.components.draw'))
                                    ->visible(fn() => auth()->user()->doctor()),
                                Textarea::make('management')
                                    ->label('Plan')
                                    ->columnSpanFull()
                                    ->autosize()
                                    ->required()
                                    ->visible(fn() => auth()->user()->doctor())
                                    ->columnSpan([
                                        'xl' => 'full'
                                    ]),
                                FileUpload::make('attachments')
                                    ->multiple()
                                    ->panelLayout('grid')
                                    ->imageEditor()
                                    ->columnSpanFull()
                                    ->removeUploadedFileButtonPosition('right')
                                    ->openable()
                                    ->imagePreviewHeight('250')
                                    ->rules([
                                        fn(): \Closure => function (string $attribute, $value, \Closure $fail) {
                                            // Skip validation if it's already a stored path (string)
                                            if (is_string($value)) {
                                                return;
                                            }

                                            // Validate new uploads
                                            if ($value instanceof \Illuminate\Http\UploadedFile && !str_starts_with($value->getMimeType(), 'image/')) {
                                                $fail('The file must be an image.');
                                            }
                                        },
                                    ]),
                                Flex::make([
                                    DatePicker::make('next_follow_up_schedule')
                                        ->label('Follow up schedule')
                                        // ->inlineLabel()
                                        ->visible(fn($livewire) => auth()->user()->can('addFollowupSchedule', $livewire->record)),
                                    TextInput::make('fee')
                                        ->label('Consultation Fee')
                                ])
                                ->columnSpanFull(),

                                Section::make('Prescriptions')
                                    ->columnSpan(2)
                                    ->visible(fn() => auth()->user()->hasRole('Doctor') || auth()->user()->superAdmin())
                                    ->compact()
                                    ->schema([
                                        Repeater::make('medicines')
                                            ->relationship('consultationMedicines')
                                            ->label('Prescription')
                                            ->addAction(function (Action $action) {
                                                return $action
                                                    ->label('Add medicine to prescription')
                                                    ->icon('healthicons-o-medicines')
                                                    ->color('primary')
                                                    ->link()
                                                    ->size('lg');
                                            })
                                            ->reorderable()
                                            // ->reorderAction(function(Action $action, $livewire) {
                                            //     $action->action(function($action) use($livewire){
                                            //         dd($livewire);
                                            //     });
                                            // })
                                            ->default(fn($state) => is_array($state) ? $state : [])
                                            ->reorderable()
                                            ->orderColumn()
                                            ->compact()
                                            ->hiddenLabel()
                                            // ->label('Prescription')
                                            // ->columns(1)
                                            ->defaultItems(0)
                                            // ->columnSpanFull()
                                            ->table([
                                                TableColumn::make('Generic'),
                                                TableColumn::make('Instruction'),
                                                TableColumn::make('Qty')
                                                    ->width('65px'),
                                            ])
                                            ->schema([
                                                Select::make('medicine_id')
                                                    ->label('Medicine')
                                                    ->relationship(
                                                        'medicine',
                                                        'name',
                                                        // modifyQueryUsing: fn(Builder $query) => $query->where('active', 1)
                                                    )
                                                    // ->preload()
                                                    ->getOptionLabelFromRecordUsing(fn(Model $record) => "{$record->name}" . ($record->brand ? ' - ' . "<b>{$record->brand}</b>" : ''))
                                                    ->allowHtml()
                                                    // ->preload()
                                                    ->searchable(['name', 'brand'])
                                                    // ->searchable(function (Builder $query, $search): Builder {
                                                    //     return $query
                                                    //         ->where('brand', 'like', "%{$search}%")
                                                    //             ->orWhere('name', 'like', "%{$search}%");

                                                    // })
                                                    ->getSearchResultsUsing(function (string $search) {
                                                        // dd($search);
                                                        return Medicine::where(function ($q) use ($search) {
                                                            $q->where('brand', 'like', "%{$search}%")
                                                                ->orWhere('name', 'like', "%{$search}%");
                                                        })
                                                            ->where('active', 1)
                                                            ->limit(20)
                                                            ->get()
                                                            ->map(fn($item) => [
                                                                'id' => $item->id,
                                                                'name' => "
                                                                        <div class='flex items-center gap-2'><div>$item->name</div><div class='text-indigo-500'>($item->brand)</div></div>
                                                                    "
                                                            ])
                                                            ->pluck('name', 'id')
                                                            ->toArray();
                                                    })
                                                    ->allowHtml()
                                                    ->required()
                                                    ->createOptionForm(function (Schema $schema) {
                                                        return MedicineResource::form($schema)->extraAttributes(['class' => 'w-full']);
                                                    })
                                                    ->createOptionAction(function (Action $action) {
                                                        return $action
                                                            ->modalHeading('Add Medicine')
                                                            ->mutateDataUsing(function (array $data) {
                                                                $data['user_id'] = auth()->id();
                                                                return $data;
                                                            });
                                                    })
                                                    ->editOptionForm(function (Schema $schema) {
                                                        return MedicineResource::form($schema)->extraAttributes(['class' => 'w-full']);
                                                    })
                                                    ->editOptionAction(function (Action $action, $state) {
                                                        Medicine::with('consultations')->find($state);
                                                        return $action
                                                            ->visible(fn($state) => Medicine::with('consultations')->find($state)?->consultations->isEmpty());
                                                    })
                                                    ->columnSpan([
                                                        'lg' => 2,
                                                    ]),
                                                TextInput::make('remarks')
                                                    ->datalist(fn() => ConsultationMedicine::distinct('remarks')->pluck('remarks')->toArray())
                                                    ->required()
                                                    ->columnSpan([
                                                        'lg' => 2
                                                    ]),
                                                TextInput::make('quantity')
                                                    ->required()

                                                    ->columnSpan(2)
                                                    ->columnSpan([
                                                        'lg' => 1,
                                                        // 'xl' => 1
                                                    ]),
                                            ])
                                    ])
                                    ->columnSpan(2)
                                    ->visible(fn() => auth()->user()->doctor()),

                            ]),
                        Section::make()
                            ->columnSpan(1)
                            ->columns([
                                'default' => 1
                            ])
                            ->schema([
                                PatientField::make('patient_detail')
                                    ->columnSpanFull(),
                                PatientField::make('patient_history')
                                    ->columnSpanFull(),
                                PatientField::make('hospital_admission')
                                    ->columnSpanFull(),
                            ])
                    ]),
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
