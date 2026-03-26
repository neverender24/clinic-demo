<?php

use Illuminate\Http\Request;
use App\Filament\Pages\Auth\Login;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Spatie\Activitylog\Models\Activity;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\Isaiah\MedCertController;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('print-prescription/{id}', [ConsultationController::class, 'print']);

Route::get('medcert/{id}', [ConsultationController::class, 'medcert']);

Route::get('/print-view/{consultation}', [ConsultationController::class, 'prescription'])->name('prescription.print');
Route::get('medical-cert/{id}', [ConsultationController::class, 'medcert'])->name('medical.medcert');
Route::get('/admitting-order/{consultation}', [ConsultationController::class, 'admittingOrder'])->name('prescription.admitting.order');
Route::get('custom_docs/{doc}', [ConsultationController::class, 'customDoc'])->name('print.custom.doc');

Route::get('/prescription/{id}', [App\Http\Controllers\LetterPaperController::class, 'generate'])->name('tcpdf.print');
Route::get('/afive-doc/{id}', [App\Http\Controllers\AFivePaperController::class, 'generate'])->name('tcpdf.print.a5');
Route::get('/afive-mercert/{id}', [MedCertController::class, 'generate'])->name('tcpdf.print.a5-medcert');

// Blade for new tab
Route::get('/pdf/new-tab', function () {
    if (request('paper') == 'A5') {
        # code...
        return view('pdf.print.a5', [
            'id' => request('id'),
            'paper' => request('paper'),
            'type' => request('type'),
            'custom_doc_id' => request('custom_doc_id'),
            'batch' => request('batch'),
        ]);
    }
    return view('pdf.new-tab', [
        'id' => request('id'),
        'paper' => request('paper'),
        'type' => request('type'),
    ]);
})->name('pdf.new-tab');

Route::get('/pdf/medcert', function () {
    return view('pdf.isaiah.medcert', [
        'id' => request('id'),
        'paper' => request('paper'),
        'type' => request('type'),
    ]);
})->name('pdf.a5.medcert');


Route::post('/save-header-image', function (Request $request) {
    $data = $request->input('image');
    $template = $request->input('template');
    $type = $request->input('type');
    $clinicId = $request->input('clinic_id');

    if (!$data || !str_starts_with($data, 'data:image')) {
        return response()->json(['status' => 'error', 'message' => 'Invalid image data']);
    }

    $image = str_replace('data:image/png;base64,', '', $data);
    $image = str_replace(' ', '+', $image);
    $imageData = base64_decode($image);

    $folder = $clinicId ? "images/clinic_{$clinicId}" : "images";
    $path = public_path("{$folder}/{$type}_header.png");
    if (!file_exists(dirname($path))) {
        mkdir(dirname($path), 0777, true);
    }

    file_put_contents($path, $imageData);

    // Extract width and height from template JSON
    $templateData = json_decode($template, true);
    $width = $templateData['width'] ?? null;
    $height = $templateData['height'] ?? null;

    DB::table('headers')->updateOrInsert(
        ['type' => $type, 'clinic_id' => $clinicId],
        [
            'file_path' => "{$folder}/{$type}_header.png",
            'template_json' => $template,
            'width' => $width,
            'height' => $height,
            'updated_at' => now(),
        ]
    );

    return response()->json(['status' => 'success', 'path' => "{$folder}/{$type}_header.png"]);
});

Route::get('/get-header-template/{type}', function ($type) {
    $clinicId = request('clinic_id');
    $header = DB::table('headers')
        ->where('type', $type)
        ->where('clinic_id', $clinicId)
        ->first();

    if (!$header) {
        return response()->json(['template_json' => null, 'file_path' => null]);
    }

    return response()->json([
        'template_json' => $header->template_json,
        'file_path' => $header->file_path,
    ]);
});
