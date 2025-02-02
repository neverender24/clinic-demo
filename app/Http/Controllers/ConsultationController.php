<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Scopes\TenantScope;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    public function print($id)
    {
        $record = Consultation::withoutGlobalScope(TenantScope::class)->find($id);
       
        return view('consultations.medcert1', [
            'medicines' => $record->medicines,
            'patient' => $record->patient,
            'next_follow_up_schedule' => $record->next_follow_up_schedule->format('F j, Y'),
            'header_image' => asset('storage/'.$record->clinic->header_image),
            'watermark' => asset('images/logo/sto_tomas_logo.jpeg'),
            'consultation_date' => $record->date->format('F d, Y')
        ]);
    }
}
