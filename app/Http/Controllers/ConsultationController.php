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
        // dd($record);
        return view('consultations.print', [
            'medicines' => $record->medicines,
            'patient' => $record->patient,
            'next_follow_up_schedule' => $record->next_follow_up_schedule->format('F j, Y')
        ]);
    }
}
