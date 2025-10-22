<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\CustomDoc;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;
use App\Models\Scopes\TenantScope;
use Rmunate\Utilities\SpellNumber;
use App\Models\Scopes\ConsultationScope;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\Support\HtmlString;

class ConsultationController extends Controller
{
    public function print($id)
    {
        $record = $this->getRecord($id);
       
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

    public function prescription($id)
    {
        $record = $this->getRecord($id);
        $bday = Carbon::parse($record->patient?->birthday);
        $record->patient->age = $bday->age == 0 ? $bday->month : $bday->age;
        // dd($record->clinic->header_image);
        // dd(storage_path($record->clinic->header_image));
        return view('pdf.prescription', [
            'medicines' => $record->medicines->chunk(6),
            'patient' => $record->patient,
            'next_follow_up_schedule' => $record->next_follow_up_schedule?->format('F j, Y'),
            'header_image' => $record->clinic->header_image,
            'ptr' => $record->ptr
        ]);
    }

    public function medcert( $id)
    {
        // $record = Consultation::withoutGlobalScopes([TenantScope::class])->with('medicines', 'patient')->find($id);
        // return view('consultations.medcert', [
        //     'medicines' => $record->medicines,
        //     'patient' => $record->patient,
        //     'next_follow_up_schedule' => $record->next_follow_up_schedule?->format('F j, Y'),
        //     'header_image' => asset('storage/'.$record->clinic->medcert_header_image),
        //     'watermark' => asset('storage/'.$record->clinic->watermarks),
        //     'consultation_date' => $record->date?->format('F d, Y'),
        //     'medical_cert_remarks' => $record->medical_cert_remarks,
        //     'approximate_days' => $record->approximate_days,
        //     'estimated_date' => $record->estimated_date ? $record->estimated_date->format('F j, Y') : '',
        //     'diagnosis' => $record->diagnosis
        // ]);

        $record = $this->getRecord($id);
        $bday = Carbon::parse($record->patient?->birthday);
        $record->patient->age = $bday->age == 0 ? $bday->month : $bday->age;
        $record->diagnosis = str_replace(['<p>', '</p>'], ['<span>', '</span>'], $record->diagnosis);
        $total = $record->fee + floatval($record->follow_up_fees) + floatval($record->procedure_fee);
        // dd(Number::percentage($record->discount));
        $record->total = $total - ($total *  floatval("0.{$record->discount}"));
        $record->total_in_words = SpellNumber::value($record->total)->locale('en')->currency('pesos')->toMoney();
        $record->total = Number::currency($record->total, 'PHP');
        $header_image = $record->clinic->header_image;
        return view('pdf.medical-cert', compact('record', 'header_image'));
    }

    public function admittingOrder($id)
    {
        $record = $this->getRecord($id);
        $bday = Carbon::parse($record->patient?->birthday);
        $record->patient->age = $bday->age == 0 ? $bday->month : $bday->age;
        return view('consultations.admitting-order', [
            'data' => $record->admitting_order_data,
            'medicines' => $record->medicines->chunk(6),
            'patient' => $record->patient,
            'next_follow_up_schedule' => $record->next_follow_up_schedule?->format('F j, Y'),
            'header_image' => $record->clinic->header_image,
        ]);
    }

    public function customDoc(CustomDoc $doc)
    {

        $record = $this->getRecord($doc->consultation_id);

        return view('pdf.custom-doc', [
            'record' => $record,
            'content' => RichContentRenderer::make($doc->content)
                ->mergeTags([
                    'name' => $record->patient->full_name,
                    'diagnosis' => new HtmlString($record->diagnosis)
                ])
                ->toHtml(), 
        ]);
    }

    protected function getRecord($id)
    {
        return Consultation::withoutGlobalScopes([TenantScope::class, ConsultationScope::class])->with('medicines', 'clinic', 'patient')->find($id);
    }
}
