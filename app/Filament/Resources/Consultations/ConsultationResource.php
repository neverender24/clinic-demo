<?php

namespace App\Filament\Resources\Consultations;

use Filament\Tables\Table;
use App\Models\Consultation;
use Filament\Schemas\Schema;
use App\Trait\HasStatusAction;
use Filament\Resources\Resource;
use App\Filament\Resources\Consultations\Pages\CustomDoc;
use App\Filament\Resources\Consultations\Pages\EditWithHistory;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use App\Filament\Resources\Consultations\Pages\EditConsultation;

use App\Filament\Resources\Consultations\Pages\ListConsultations;
use App\Filament\Resources\Consultations\Pages\CreateConsultation;
use App\Filament\Resources\Consultations\Schemas\ConsultationForm;
use App\Filament\Resources\Consultations\Schemas\ConsultationInfolist;
use App\Filament\Resources\Consultations\Tables\ConsultationsTable;

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
        return ConsultationForm::configure($schema)->columns(1);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ConsultationInfolist::configure($schema);
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
