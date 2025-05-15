<?php

namespace App\Forms\Components;

use App\Models\Consultation;
use Filament\Forms\Components\Field;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class HistoryField extends Field
{
    protected string $view = 'filament.hospital_admission.history';

    public $patient_id = 1;
}
