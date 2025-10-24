<?php

use Illuminate\Http\Request;
use App\Filament\Pages\Auth\Login;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Spatie\Activitylog\Models\Activity;
use App\Http\Controllers\ConsultationController;

Route::get('/', Login::class);

Route::get('print-prescription/{id}', [ConsultationController::class, 'print']);

Route::get('medcert/{id}', [ConsultationController::class, 'medcert']);

Route::get('/print-view/{consultation}', [ConsultationController::class, 'prescription'])->name('prescription.print');
Route::get('medical-cert/{id}', [ConsultationController::class, 'medcert'])->name('medical.medcert');
Route::get('/admitting-order/{consultation}', [ConsultationController::class, 'admittingOrder'])->name('prescription.admitting.order');
Route::get('custom_docs/{doc}', [ConsultationController::class, 'customDoc'])->name('print.custom.doc');

Route::post('/save-header-image', function (Request $request) {
    $data = $request->input('image');
    $template = $request->input('template');
    $type = $request->input('type');

    if (!$data || !str_starts_with($data, 'data:image')) {
        return response()->json(['status' => 'error', 'message' => 'Invalid image data']);
    }

    $image = str_replace('data:image/png;base64,', '', $data);
    $image = str_replace(' ', '+', $image);
    $imageData = base64_decode($image);

    $path = public_path("images/{$type}_header.png");
    if (!file_exists(dirname($path))) {
        mkdir(dirname($path), 0777, true);
    }

    file_put_contents($path, $imageData);

    DB::table('headers')->updateOrInsert(
        ['type' => $type],
        [
            'file_path' => "images/{$type}_header.png",
            'template_json' => $template,
            'updated_at' => now(),
        ]
    );

    return response()->json(['status' => 'success', 'path' => "images/{$type}_header.png"]);
});

Route::get('/get-header-template/{type}', function ($type) {
    $header = DB::table('headers')->where('type', $type)->first();

    if (!$header) {
        return response()->json(['template_json' => null, 'file_path' => null]);
    }

    return response()->json([
        'template_json' => $header->template_json,
        'file_path' => $header->file_path,
    ]);
});
