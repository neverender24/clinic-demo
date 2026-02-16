<?php

namespace App\Filament\Resources\Consultations\Pages;

use App\Filament\Resources\Consultations\ConsultationResource;
use App\Models\Consultation;
use App\Models\CustomDoc as ModelsCustomDoc;
use App\Models\Patient;
use Asmit\FilamentMention\Forms\Components\RichMentionEditor;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use Illuminate\View\View;
use Torgodly\Html2Media\Tables\Actions\Html2MediaAction;

class CustomDoc extends ManageRelatedRecords implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithRecord;
    use InteractsWithTable;

    protected static string $resource = ConsultationResource::class;

    protected static string $relationship = 'customDocs';

    protected string $view = 'filament.resources.consultation-resource.pages.custom-doc';

    // public function mount($record)
    // {
    //     $this->record = Consultation::findOrFail($record);
    // }

    public function getTitle(): string|Htmlable
    {
        return 'Custom Documents';
    }

    public function form(Schema $schema): Schema
    {

        return $schema
            ->components([
                TextInput::make('doc_name')
                    ->label('New Document'),
                Select::make('size')
                    ->label('Size')
                    ->options([
                        'A5' => 'A5',
                    ])
                    ->default('A5'),
                RichEditor::make('content')
                    ->mergeTags([
                        'name',
                        'diagnosis',
                    ])
                    ->json()
                    ->columnSpanFull(),
                // RichMentionEditor::make('content')
                //     ->mentionsItems(function () {
                //         return Patient::all()->map(function ($user) {
                //             return [
                //                 'display_name' => $user->full_name,
                //                 'name' => $user->full_name,
                //                 'address' => $user->address,
                //                 'avatar' => asset('images/user.svg'),
                //                 'url' => 'admin/users/' . $user->id,
                //             ];
                //         })->toArray();
                //     })
                //     ->columnSpanFull()
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(ModelsCustomDoc::with('consultation.patient')->where('consultation_id', $this->record->id))
            ->columns([
                TextColumn::make('doc_name')
                    ->label('Document'),
                TextColumn::make('content1')
                    ->default(fn ($record) => RichContentRenderer::make($record->content)
                        ->mergeTags([
                            'name' => $record->consultation?->patient?->full_name,
                            'diagnosis' => new HtmlString($record->consultation?->diagnosis),
                        ])
                        ->toHtml()
                    )
                    ->html(),
                // ->toEmbeddedHtml()

                // ->formatStateUsing(fn($state) => dd($state))
            ])
            ->headerActions([
                CreateAction::make('create')
                    ->schema(fn ($form) => $this->form($form))
                    ->mutateDataUsing(function ($data) {
                        $data['consultation_id'] = $this->record->id;

                        // dd($data);
                        return $data;
                    }),
                // ->action(function($data) {
                //     $data['consultation_id'] = $this->record->id;
                //     // dd($data);
                //     $this->record
                //         ->customDocs()
                //         ->create($data);
                //     Notification::make()
                //         ->success()
                //         ->title('Custom Doc')
                //         ->body('Successfully added');
                // })
            ])
            ->recordActions([
                EditAction::make()
                    ->schema(fn ($form) => $this->form($form))
                    ->url(''),
                DeleteAction::make(),
                Action::make('print')
                    ->url(fn ($record) => route('print.custom.doc', [$record->id]), true),
                // Html2MediaAction::make('print')
                //     ->label(fn($record) => 'Print ')
                //     ->color('success')
                //     ->modalWidth(Width::MaxContent)
                //     ->icon('heroicon-o-printer')
                //     ->format('a5')
                //     ->content(function($record): View {
                //         // dd($record->admitting_order_data);
                //         // dd($record);
                //         return view('consultations.admitting-order', [
                //             'data' => $record->content,
                //             'header_image' => $this->record->clinic->header_image,
                //             'title' => $record->doc_name,
                //             'patient' => $record->consultation->patient
                //         ]);
                //     })
                //     ->preview()
            ]);
    }
}
