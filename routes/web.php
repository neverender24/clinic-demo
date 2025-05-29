<?php

use App\Filament\Pages\Auth\Login;
use App\Http\Controllers\ConsultationController;
use Illuminate\Support\Facades\Route;

Route::get('/', Login::class);

Route::get('print-prescription/{id}', [ConsultationController::class, 'print']);