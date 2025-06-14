<?php

use App\Filament\Pages\Auth\Login;
use Illuminate\Support\Facades\Route;
use Spatie\Activitylog\Models\Activity;
use App\Http\Controllers\ConsultationController;

Route::get('/', Login::class);

Route::get('print-prescription/{id}', [ConsultationController::class, 'print']);

