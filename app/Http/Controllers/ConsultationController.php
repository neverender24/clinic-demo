<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Scopes\ConsultationScope;
use App\Models\Scopes\TenantScope;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    public function print($id)
    {
        $record = Consultation::withoutGlobalScope(TenantScope::class)->find($id);
       
        $meds = $record->medicines->chunk(3);
        
        return view('consultations.sample', [
            'medicines' => $record->medicines,
            'patient' => $record->patient,
            'next_follow_up_schedule' => $record->next_follow_up_schedule?->format('F j, Y'),
            'header_image' => asset('storage/'.$record->clinic->header_image),
            'watermark' => asset('images/logo/sto_tomas_logo.jpeg'),
            'consultation_date' => $record->date?->format('F d, Y')
        ]);
    }

    public function medcert( $id)
    {
        $record = Consultation::withoutGlobalScopes([TenantScope::class, ConsultationScope::class])->with('medicines', 'patient')->find($id);
        return view('consultations.medcert', [
            'medicines' => $record->medicines,
            'patient' => $record->patient,
            'next_follow_up_schedule' => $record->next_follow_up_schedule?->format('F j, Y'),
            'header_image' => asset('storage/'.$record->clinic->medcert_header_image),
            'watermark' => asset('storage/'.$record->clinic->watermarks),
            'consultation_date' => $record->date?->format('F d, Y'),
            'medical_cert_remarks' => $record->medical_cert_remarks,
            'approximate_days' => $record->approximate_days,
            'estimated_date' => $record->estimated_date ? $record->estimated_date->format('F j, Y') : '',
            'diagnosis' => $record->diagnosis
        ]);
    }
}
