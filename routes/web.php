<?php

use App\Filament\Pages\Auth\Login;
use Illuminate\Support\Facades\Route;
use Spatie\Activitylog\Models\Activity;
use App\Http\Controllers\ConsultationController;

Route::get('/', Login::class);

Route::get('print-prescription/{id}', [ConsultationController::class, 'print']);

Route::get('medcert/{id}', [ConsultationController::class, 'medcert']);

Route::get('/print-view/{consultation}', [ConsultationController::class, 'prescription'])->name('prescription.print');
Route::get('medical-cert/{id}', [ConsultationController::class, 'medcert'])->name('medical.medcert');
Route::get('/admitting-order/{consultation}', [ConsultationController::class, 'admittingOrder'])->name('prescription.admitting.order');