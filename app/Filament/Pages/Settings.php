<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Facades\Filament;
use App\Models\ClinicSetting;
use Filament\Actions\Action;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\CheckboxList;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;

class Settings extends Page
{
    protected string $view = 'filament.pages.settings';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string | \UnitEnum | null $navigationGroup = 'Administration';

    protected static ?string $title = 'Settings';

    protected static ?int $navigationSort = 10;

    public array $data = [];

    public function mount(): void
    {
        $this->data = ClinicSetting::getForCurrentClinic();

        $this->form->fill($this->data);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Tabs::make('Settings')
                    ->tabs([
                        Tabs\Tab::make('Consultation')
                            ->icon('healthicons-o-telemedicine')
                            ->schema([
                                Section::make('Form Fields')
                                    ->description('Toggle which fields appear in the consultation form.')
                                    ->icon('heroicon-o-rectangle-stack')
                                    ->columns(2)
                                    ->schema([
                                        Toggle::make('consultation.show_dialysis')
                                            ->label('Dialysis')
                                            ->helperText('Show the Dialysis toggle on the consultation form')
                                            ->inline(false),

                                        Toggle::make('consultation.show_consultation_fee')
                                            ->label('Consultation Fee')
                                            ->helperText('Show the Consultation Fee field')
                                            ->inline(false),

                                        Toggle::make('consultation.show_patient_section')
                                            ->label('Patient Details Sidebar')
                                            ->helperText('Show or hide the entire patient details panel on the right')
                                            ->inline(false)
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('SOAP Labels')
                                    ->description('Customize the display labels and editor type for each SOAP note field.')
                                    ->icon('heroicon-o-pencil-square')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('consultation.label_subjective')
                                            ->label('Subjective label')
                                            ->placeholder('Subjective')
                                            ->maxLength(60),

                                        Select::make('consultation.editor_subjective')
                                            ->label('Subjective editor')
                                            ->options(['textarea' => 'Plain Textarea', 'richeditor' => 'Rich Editor'])
                                            ->default('textarea')
                                            ->selectablePlaceholder(false),

                                        TextInput::make('consultation.label_objective')
                                            ->label('Objective label')
                                            ->placeholder('Objective')
                                            ->maxLength(60),

                                        Select::make('consultation.editor_objective')
                                            ->label('Objective editor')
                                            ->options(['textarea' => 'Plain Textarea', 'richeditor' => 'Rich Editor'])
                                            ->default('textarea')
                                            ->selectablePlaceholder(false),

                                        TextInput::make('consultation.label_assessment')
                                            ->label('Assessment label')
                                            ->placeholder('Assessment')
                                            ->maxLength(60),

                                        Select::make('consultation.editor_assessment')
                                            ->label('Assessment editor')
                                            ->options(['textarea' => 'Plain Textarea', 'richeditor' => 'Rich Editor'])
                                            ->default('textarea')
                                            ->selectablePlaceholder(false),

                                        TextInput::make('consultation.label_plan')
                                            ->label('Plan label')
                                            ->placeholder('Plan')
                                            ->maxLength(60),

                                        Select::make('consultation.editor_plan')
                                            ->label('Plan editor')
                                            ->options(['textarea' => 'Plain Textarea', 'richeditor' => 'Rich Editor'])
                                            ->default('textarea')
                                            ->selectablePlaceholder(false),
                                    ]),

                                Section::make('Patient Details Sidebar')
                                    ->description('Toggle which individual fields are visible inside the patient details panel.')
                                    ->icon('heroicon-o-user')
                                    ->columns(2)
                                    ->schema([
                                        Toggle::make('consultation.show_smoker_type')
                                            ->label('Smoker Type')
                                            ->helperText('Show smoking status (Smoker, Non-smoker, Vaper, Quitter)')
                                            ->inline(false),

                                        Toggle::make('consultation.show_allergies')
                                            ->label('Allergies')
                                            ->helperText('Show patient allergies')
                                            ->inline(false),

                                        Toggle::make('consultation.show_surgeries')
                                            ->label('Surgeries')
                                            ->helperText('Show previous surgeries')
                                            ->inline(false),

                                        Toggle::make('consultation.show_medical_conditions')
                                            ->label('Medical Conditions')
                                            ->helperText('Show existing medical conditions')
                                            ->inline(false),

                                        Toggle::make('consultation.show_medications')
                                            ->label('Medications')
                                            ->helperText('Show current medications')
                                            ->inline(false),

                                        Toggle::make('consultation.show_birthday')
                                            ->label('Birthday')
                                            ->helperText('Show patient date of birth')
                                            ->inline(false),

                                        Toggle::make('consultation.show_age')
                                            ->label('Age')
                                            ->helperText('Show patient age (calculated from birthday)')
                                            ->inline(false),

                                        Toggle::make('consultation.show_sex')
                                            ->label('Sex')
                                            ->helperText('Show patient sex (Male / Female)')
                                            ->inline(false),

                                        Toggle::make('consultation.show_civil_status')
                                            ->label('Civil Status')
                                            ->helperText('Show civil status (Single, Married, etc.)')
                                            ->inline(false),

                                        Toggle::make('consultation.show_address')
                                            ->label('Address')
                                            ->helperText('Show patient home address')
                                            ->inline(false),

                                        Toggle::make('consultation.show_occupation')
                                            ->label('Occupation')
                                            ->helperText('Show patient occupation')
                                            ->inline(false),
                                    ]),
                            ]),

                        Tabs\Tab::make('Dashboard')
                            ->icon('heroicon-o-squares-2x2')
                            ->schema([
                                Section::make('Widgets')
                                    ->description('Toggle widget visibility and drag the handle to reorder. Changes apply on next Dashboard load.')
                                    ->icon('heroicon-o-view-columns')
                                    ->schema([
                                        Repeater::make('dashboard.widgets')
                                            ->hiddenLabel()
                                            ->addable(false)
                                            ->deletable(false)
                                            ->reorderable()
                                            ->itemLabel(fn (array $state): string => $state['label'] ?? 'Widget')
                                            ->schema([
                                                Hidden::make('key'),
                                                Hidden::make('label'),
                                                Toggle::make('visible')
                                                    ->label('Show on Dashboard')
                                                    ->inline(false),
                                            ]),
                                    ]),
                            ]),

                        Tabs\Tab::make('Patients')
                            ->icon('heroicon-o-user-group')
                            ->schema([
                                Section::make('Medical Condition Suggestions')
                                    ->description('Manage the list of suggestions shown in the Medical Conditions field on the patient form. Drag to reorder.')
                                    ->icon('heroicon-o-heart')
                                    ->schema([
                                        Repeater::make('patients.medical_condition_suggestions')
                                            ->table([
                                                TableColumn::make('Condition name'),
                                            ])
                                            ->hiddenLabel()
                                            ->reorderable()
                                            ->addActionLabel('Add condition')
                                            ->schema([
                                                TextInput::make('name')
                                                    ->label('Condition name')
                                                    ->required()
                                                    ->maxLength(150)
                                                    ->placeholder('e.g. Hypertension'),
                                            ])
                                            ->itemLabel(fn (array $state): string => $state['name'] ?? 'New condition'),
                                    ]),
                            ]),

                        Tabs\Tab::make('Clinical Orders')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Section::make('Laboratory Request — Common Tests')
                                    ->description('Manage the list of tests shown as checkboxes in the Laboratory Request form. Drag to reorder.')
                                    ->icon('heroicon-o-beaker')
                                    ->schema([
                                        Repeater::make('clinical_orders.lab_tests')
                                            ->table([
                                                TableColumn::make('Test name'),
                                            ])
                                            ->hiddenLabel()
                                            ->reorderable()
                                            ->addActionLabel('Add test')
                                            ->schema([
                                                TextInput::make('name')
                                                    ->label('Test name')
                                                    ->required()
                                                    ->maxLength(150)
                                                    ->placeholder('e.g. CBC'),
                                            ])
                                            ->itemLabel(fn (array $state): string => $state['name'] ?? 'New test'),
                                    ]),
                            ]),

                        Tabs\Tab::make('Reports')
                            ->icon('heroicon-o-document-chart-bar')
                            ->schema([
                                Section::make('Medical Certificate Template')
                                    ->description('Design the content and layout of the medical certificate. Use merge tags like {{name}}, {{age}}, {{sex}}, {{address}}, {{date}}, {{diagnosis}} to insert patient data.')
                                    ->icon('heroicon-o-document-text')
                                    ->schema([
                                        Select::make('reports.med_cert.paper_size')
                                            ->label('Paper Size')
                                            ->options([
                                                'A5' => 'A5',
                                                'A4' => 'A4',
                                                'legal' => 'Legal',
                                                'letter' => 'Letter',
                                            ])
                                            ->default('letter')
                                            ->selectablePlaceholder(false),

                                        Toggle::make('reports.med_cert.with_header')
                                            ->label('Include Patient Details Header')
                                            ->helperText('When enabled, patient details (Name, Date, Age, Address, Sex) will be printed above the content.')
                                            ->inline(false)
                                            ->default(true)
                                            ->live(),

                                        CheckboxList::make('reports.med_cert.header_fields')
                                            ->label('Header Fields')
                                            ->helperText('Select which patient details to include in the header.')
                                            ->options([
                                                'name' => 'Name',
                                                'date' => 'Date',
                                                'age' => 'Age',
                                                'address' => 'Address',
                                                'sex' => 'Sex',
                                                'consultation_date' => 'Date of Consultation',
                                            ])
                                            ->default(['name', 'date', 'age', 'address', 'sex'])
                                            ->columns(3)
                                            ->visible(fn ($get) => $get('reports.med_cert.with_header')),

                                        TextEntry::make('merge_tags_hint')
                                            ->label('Available Merge Tags')
                                            ->default('Use these tags in the content below and they will be replaced with actual patient data when printing: {{name}}, {{age}}, {{sex}}, {{address}}, {{date}}, {{consultation_date}}, {{diagnosis}}, {{remarks}}'),

                                        RichEditor::make('reports.med_cert.content')
                                            ->label('Certificate Content')
                                            ->helperText('Design the body of the medical certificate. Use merge tags to insert dynamic patient data.')
                                            ->mergeTags([
                                                'name',
                                                'age',
                                                'sex',
                                                'address',
                                                'date',
                                                'consultation_date',
                                                'diagnosis',
                                                'remarks',
                                            ])
                                            ->json()
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Settings')
                ->icon('heroicon-o-check')
                ->action('save'),
        ];
    }

    public function save(): void
    {
        $clinic = Filament::getTenant();

        ClinicSetting::updateOrCreate(
            ['clinic_id' => $clinic->id],
            ['data' => $this->form->getState()],
        );

        ClinicSetting::clearCache();

        Notification::make()
            ->title('Settings saved successfully.')
            ->success()
            ->send();
    }
}
