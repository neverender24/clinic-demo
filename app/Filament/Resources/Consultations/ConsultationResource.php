<?php

namespace App\Filament\Resources\Consultations;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Schemas\Components\Fieldset;
use Throwable;
use Filament\Support\Enums\Width;
use App\Filament\Resources\Consultations\Pages\EditConsultation;
use App\Filament\Resources\Consultations\Pages\CustomDoc;
use App\Filament\Resources\Consultations\Pages\EditWithHistory;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Patient;
use App\Models\Medicine;
use Illuminate\View\View;
use Filament\Tables\Table;
use Livewire\Attributes\On;
use App\Models\Consultation;
use Livewire\Attributes\Url;
use App\Trait\HasStatusAction;
use Filament\Resources\Resource;
use Filament\Actions\ActionGroup;
use function Laravel\Prompts\form;
use Filament\Tables\Filters\Filter;
use App\Models\ConsultationMedicine;
use Filament\Forms\Components\Select;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Livewire;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\ConsultationScope;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Livewire\Consultation\ListRecords;
use Filament\Forms\Components\MarkdownEditor;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\View as ComponentsView;
use App\Filament\Resources\ConsultationResource\Pages;
use App\Filament\Resources\Consultations\Pages\CreateConsultation;
use Torgodly\Html2Media\Tables\Actions\Html2MediaAction;

use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use App\Filament\Resources\ConsultationResource\RelationManagers;
use App\Filament\Resources\Consultations\Pages\ListConsultations;
use App\Filament\Resources\Consultations\Schemas\ConsultationForm;
use App\Filament\Resources\Consultations\Tables\ConsultationsTable;
use App\Forms\Components\HistoryField;
use App\Models\HospitalAdmission;
use Filament\Facades\Filament;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Set;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Number;

class ConsultationResource extends Resource implements HasShieldPermissions
{
    use HasStatusAction;

    protected static ?string $model = Consultation::class;

    protected static string | \BackedEnum | null $navigationIcon = 'healthicons-o-telemedicine';

    protected static bool $shouldRegisterNavigation = true;
    

    public static function  getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'restore',
            'restore_any',
            'replicate',
            'reorder',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any',
            'add_management',
            'add_diagnosis',
            'add_chief_complaint',
            'add_prescription',
            'add_test_results',
            'edit_as_doctor',
            'add_followup_schedule'
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return ConsultationForm::configure($schema);
    }

   
    public static function table(Table $table): Table
    {
        return ConsultationsTable::configure($table);
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
            // 'index' => Pages\CustomListConsultations::route('/'),
            'index' => ListConsultations::route('/'),
            'create' => CreateConsultation::route('/create'),
            // 'create' => Pages\CreateConsultation::route('/create'),
            'edit' => EditConsultation::route('/{record}/edit'),
            'custom.doc' => CustomDoc::route('/{record}/custom-doc'),
            'edit.consultation' => EditWithHistory::route('/{record}/edit-consultation'),
        ];
    }

    // additional methods

    public static function getNavigationBadge(): ?string
    {
        return transform(static::getModel()::query()->where('status', 'Pending')->currentConsultations()->count(), fn($value) => $value > 0 ? $value : null);
    }
}
