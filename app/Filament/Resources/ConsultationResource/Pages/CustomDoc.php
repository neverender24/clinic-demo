<?php

namespace App\Filament\Resources\ConsultationResource\Pages;

use App\Models\Patient;
use Filament\Forms\Form;
use Illuminate\View\View;
use Filament\Tables\Table;
use Filament\Resources\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\CreateAction;
use App\Models\CustomDoc as ModelsCustomDoc;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Filament\Resources\ConsultationResource;
use Filament\Tables\Concerns\InteractsWithTable;
use Torgodly\Html2Media\Tables\Actions\Html2MediaAction;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Asmit\FilamentMention\Forms\Components\RichMentionEditor;
use Illuminate\Contracts\Support\Htmlable;

class CustomDoc extends Page implements HasTable, HasForms
{
    use InteractsWithRecord;
    use InteractsWithTable;
    use InteractsWithForms;

    protected static string $resource = ConsultationResource::class;

    protected static string $view = 'filament.resources.consultation-resource.pages.custom-doc';

    public function mount($record) 
    {
        $this->record = $this->resolveRecord($record);
    }

    public function getTitle(): string|Htmlable
    {
        return "Custom Documents";
    }

    public function form(Form $form): Form
    {
        return $form
                ->schema([
                    TextInput::make('doc_name')
                        ->label('New Document'),
                    Select::make('size')
                        ->label('Size')
                        ->options([
                            'A5' => 'A5'
                        ])
                        ->default('A5'),
                    RichMentionEditor::make('content')
                        ->mentionsItems(function () {
                            return Patient::all()->map(function ($user) {
                                return [
                                    'display_name' => $user->full_name,
                                    'name' => $user->full_name,
                                    'address' => $user->address,
                                    'avatar' => asset('images/user.svg'),
                                    'url' => 'admin/users/' . $user->id,
                                ];
                            })->toArray();
                        })
                        ->columnSpanFull()
                ])
                ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
                ->query(ModelsCustomDoc::where('consultation_id', $this->record->id))
                ->columns([
                    TextColumn::make('doc_name')
                        ->label('Document')
                ])
                ->headerActions([
                    CreateAction::make()
                        ->form(fn($form) => $this->form($form))
                        ->mutateFormDataUsing(function($data) {
                            $data['consultation_id'] = $this->record->id;
                            
                            return $data;
                        })
                ])
                ->actions([
                    EditAction::make()
                        ->form(fn($form) => $this->form($form)),
                    Html2MediaAction::make('print')
                        ->label(fn($record) => 'Print ')
                        ->color('success')
                        ->modalWidth(MaxWidth::MaxContent)
                        ->icon('heroicon-o-printer')
                        ->format('a5')
                        ->content(function($record): View {
                            // dd($record->admitting_order_data);
                            return view('consultations.admitting-order', [
                                'data' => $record->content,
                                'header_image' => $this->record->clinic->header_image,
                                'title' => $record->doc_name
                            ]);
                        })
                        ->preview()
                ]);
    }
    
}
