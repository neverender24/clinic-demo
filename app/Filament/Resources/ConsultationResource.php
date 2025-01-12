<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Consultation;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ConsultationResource\Pages;
use App\Filament\Resources\ConsultationResource\RelationManagers;
use App\Models\Patient;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\Action as ActionsAction;
use Illuminate\View\View;

class ConsultationResource extends Resource
{
    protected static ?string $model = Consultation::class;

    protected static ?string $navigationIcon = 'healthicons-o-telemedicine';

   
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                Forms\Components\DatePicker::make('date')
                                    ->required(),
                                Forms\Components\Select::make('patient_id')
                                    ->label('Patient')
                                    // ->getSearchResultsUsing(fn (string $search) => Patient::query()->where('full_name', 'like', "%$search%")->pluck('full_name', 'id'))
                                    ->getOptionLabelsUsing(fn ($value) => Patient::find($value)->full_name)
                                    ->preload()
                                    ->searchable()
                                    ->relationship('patient', 'full_name')
                                    ->createOptionForm(function (Form $form) {
                                        return PatientResource::form($form)->extraAttributes(['class' => 'w-full']);
                                    })
                                    ->createOptionAction(function(Action $action) {
                                        return $action
                                            ->modalWidth('xl')
                                            ->modalHeading('Create Patient')
                                            ->mutateFormDataUsing(function(array $data) {
                                                $data['user_id'] = auth()->id();
                                                return $data;
                                            });
                                    })
                                    ->required()
                                    ->columnSpan(3),
                                Grid::make()
                                    ->schema([
                                        Forms\Components\RichEditor::make('chief_complaint')
                                            ->required()
                                            // ->columnSpanFull()
                                            ->columnSpan(2)
                                            ,
                                        Forms\Components\RichEditor::make('test_results')
                                            ->required()
                                            // ->columnSpanFull()
                                            ->columnSpan(2)
                                            ,
                                        Forms\Components\RichEditor::make('diagnosis')
                                            ->required()
                                            ->columnSpan(2),
                                        Forms\Components\RichEditor::make('management')
                                            ->required()
                                            ->columnSpan(2),
                                    ])
                                    ->columns(4),
                                    
                            ])
                            ->columns(4)
                            ->columnSpan(2),
                            Section::make('Prescriptions')
                                ->schema([
                                    Repeater::make('medicines')
                                        ->relationship('consultationMedicines')
                                        ->schema([
                                            Select::make('medicine_id')
                                                ->label('Medicine')
                                                ->relationship('medicine', 'name')
                                                ->allowHtml()
                                                ->preload()
                                                ->searchable()
                                                ->required(),
                                            TextInput::make('remarks')
                                                ->required(),
                                        ])
                                        ->columns(2)
                                        ->label('Prescription')
                                        ->defaultItems(1)
                                ])
                                
                    ])
                    // ->extraAttributes([
                    //     'class' => 'flex justify-center',
                    // ])
                    // ->columnSpanFull()
            ])
            ->columns(1)
            ->extraAttributes(['class' => '', 'id' => 'consutation-form']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->with(['medicines', 'patient']))
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('chief_complaint')
                    ->searchable()
                    ->html(),
                Tables\Columns\TextColumn::make('test_results')
                    ->searchable()
                    ->html(),
                Tables\Columns\TextColumn::make('diagnosis')
                    ->searchable()
                    ->html(),
                Tables\Columns\TextColumn::make('management')
                    ->searchable()
                    ->html(),
                Tables\Columns\TextColumn::make('clinic_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('patient_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                ActionsAction::make('print prescription')
                    ->color('success')
                    ->icon('heroicon-o-printer')
                    ->modalContent(function($record): View {
                        return view('consultations.print', [
                            'medicines' => $record->medicines,
                            'patient' => $record->patient,
                        ]);
                    })
                    ->slideOver(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConsultations::route('/'),
            'create' => Pages\CreateConsultation::route('/create'),
            'edit' => Pages\EditConsultation::route('/{record}/edit'),
        ];
    }
}
