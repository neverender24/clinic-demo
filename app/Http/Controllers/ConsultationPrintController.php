<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use Illuminate\Http\Request;

class ConsultationPrintController extends Controller
{
    public function medcert(Request $request, Consultation $consultation)
    {
        $consultation->update([
            'medical_cert_remarks' => $request->input('medical_cert_remarks'),
        ]);

        return redirect()->route('pdf.a5.medcert', [
            'id' => $consultation->id,
            'type' => 'Medical Certificate',
            'paper' => 'A5',
        ]);
    }

    public function clinical(Request $request, Consultation $consultation)
    {
        $type = $request->input('type');

        if (auth()->user()?->doctor()) {
            $typeField = match ($type) {
                'Admitting Orders' => 'admitting_order_data',
                'Referral Form' => 'referral_content',
                'Medical Abstract' => 'medical_abstract',
                default => null,
            };

            if ($typeField) {
                $consultation->update([
                    $typeField => $request->input('content'),
                ]);
            }

            if ($type === 'Laboratory Request') {
                $consultation->labRequests()->delete();

                foreach ($request->input('lab_requests', []) as $content) {
                    if (trim($content)) {
                        $consultation->labRequests()->create(['content' => $content]);
                    }
                }
            }
        }

        return redirect()->route('pdf.new-tab', [
            'id' => $consultation->id,
            'paper' => 'A5',
            'type' => $type,
        ]);
    }
}
