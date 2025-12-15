<?php

namespace App\Http\Controllers\Isaiah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Scopes\TenantScope;
use Illuminate\Support\Number;
use TCPDF;
use App\Models\Consultation;

class MedCertController extends Controller
{
    public $image_header = '';
    protected $content = '';
    protected int $fontSize;

    public function __construct()
    {
        $this->fontSize = request('paper') == 'A5' ? 8 : 12;
    }

    /** ------------------------------
     *  HEADER
     *  ------------------------------ */
    public function getHeader(): string
    {
        return '<br>';
    }

    /** ------------------------------
     *  FOOTER
     *  ------------------------------ */
    public function getFooter($isPrescription = false): string
    {
        $fontSize = $this->fontSize;
        return '
        <table width="100%" style="font-size: '.$fontSize.'; line-height:1.2;">
            <tr>
                <td width="60%" style="vertical-align:bottom; text-align:left;">
                    ' . ($isPrescription ? '<b>Next Follow-up Schedule:</b> ________________________' : '') . '
                </td>
                <td width="50%" style="text-align:left; vertical-align:bottom">
                    <b>Attending Physician:</b><br><br><br>
                    <b>Isaiah Jeremi P. Gampon, MD, FPCP</b><br>
                    License no: 0133619<br>
                    PTR no: 3326746<br>
                    S2 License no: _____________________
                </td>
            </tr>
        </table>';
    }

    /** ------------------------------
     *  MAIN PDF GENERATOR
     *  ------------------------------ */
    public function generate(Request $request, $id)
    {
        $consultation = Consultation::withoutGlobalScope(TenantScope::class)
            ->with(['patient', 'medicines'])
            ->findOrFail($id);

        $paper = strtolower($request->paper ?? 'letter');
        $this->image_header =  public_path('storage/' . $consultation->clinic->medcert_header_image);
        // $this->image_header =  public_path('images/prescription_header.png');

        $pdf = $this->setupPdf($paper);
        $headerHtml = $this->getHeader();
        $footerHtml = $this->getFooter(isPrescription: !$request->type);

        // ------------------------------ CONTENT ------------------------------
        $this->generateMedicalCertificateContent($consultation);

        // $content = $this->content;

         $this->addCustomPage($pdf, $this->content, $headerHtml, $footerHtml, true, true, $paper);

        $pdf->Output('prescription.pdf', 'I');
    }

    /** ------------------------------
     *  PAGE SETUP SEPARATION
     *  ------------------------------ */
    protected function setupPdf(string $paper): TCPDF
    {
        $pdf = new TCPDF('P', 'mm', strtoupper($paper), true, 'UTF-8', false);
        $pdf->SetCreator('TCPDF');
        $pdf->SetAuthor('Dr. Ben Jay Porcadilla');
        $pdf->SetTitle(request('type') ?? 'Prescription');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        if ($paper === 'a5') {
            $pdf->SetMargins(10, 10, 10);
            $pdf->SetAutoPageBreak(true, 5);
        }

        $pdf->SetFont('helvetica', '', $this->fontSize);
        return $pdf;
    }

    /** ------------------------------
     *  ADD PAGE WITH HEADER + FOOTER
     *  ------------------------------ */
    protected function addCustomPage($pdf, $content, $headerHtml, $footerHtml, $isFirstPage, $isLastPage, $paper)
    {
        $pdf->AddPage();

        // --- HEADER IMAGE ---
        if ($isFirstPage && file_exists($this->image_header)) {
            $margins = $pdf->getMargins();
            $leftMargin = $margins['left'];
            $pageWidth = $pdf->getPageWidth();
            $usableWidth = $pageWidth - $leftMargin - $margins['right'];

            $pdf->Image(
                $this->image_header,
                $leftMargin,
                0, // No margin top
                $usableWidth,
                0,
                '',
                '',
                '',
                false,
                300
            );

            // Reset Y properly (different for each paper type)
            $pdf->SetY(40);
        } else {
            $pdf->SetY(20);
        }

        // --- CONTENT ---
        $pdf->writeHTML($content, true, false, true, false, '');

        // --- FOOTER ---
        if ($isLastPage) {
            $pageHeight = $pdf->getPageHeight();
            $footerHeight = $paper === 'a5' ? 40 : 53;
            $footerY = $pageHeight - $footerHeight;

            if ($pdf->GetY() > $footerY - 5) {
                $pdf->AddPage();
            }

            $pdf->SetY(-$footerHeight);
            $pdf->writeHTMLCell(0, 0, '', '', $footerHtml, 0, 1, 0, true, 'R', true);
        }
    }

    /** ------------------------------
     *  PATIENT INFO
     *  ------------------------------ */
    protected function patientinfo($consultation)
    {
        $sex = $consultation->patient->sex == 'M' ? 'Male' : 'Female';
        $age = Carbon::parse($consultation->patient->birthday)->age;
        $name = $consultation->patient->full_name;
        $address = $consultation->patient->address;
        $date = now()->format('F j, Y');
        $fontSize = $this->fontSize;

        $content = '<style>
            .info-table { width:100%; font-size:'.$fontSize.'; border-collapse:collapse; }
            .info-table td { vertical-align:top; padding:1px 0; }
            .right { text-align:right; }
        </style>';

        $content .= '<table class="info-table">
            <tr>
                <td><b>Name:</b> '.$name.'</td>
                <td class="right"><b>Date:</b> '.$date.'</td>
            </tr>
            <tr>
                <td><b>Address:</b> '.$address.'</td>
                <td class="right"><b>Age:</b> '.$age.' &nbsp;&nbsp;&nbsp;&nbsp;<b>Sex:</b> '.$sex.'</td>
            </tr>';

        if (! request('type')) {
            $content .= '<tr><td colspan="2"><img src="' . public_path('images/clinic/rx.png') . '" style="width:40px;"></td></tr>';
        } else {
            $content .= '<tr><td colspan="2" style="text-align:center; font-weight:bold; font-size:' . ($fontSize+3) . ';">' . strtoupper(request('type')) . '</td></tr>';
        }

        $content .= '</table>';
        return $content;
    }

    /** ------------------------------
     *  CONTENT GENERATORS
     *  ------------------------------ */
    protected function generateCustomContent($title, $content, $consultation): void
    {
        $this->content = $this->patientinfo($consultation) .
            '<div style="font-size:9pt; line-height:1;">'.$content.'</div>';
    }

    protected function generatePrescriptionContent($consultation, $prescribe_meds): void
    {
        $content = '
        <style>
            ol.medicine-list { margin:0; padding-left:15px; line-height:1; font-size:9pt; }
            ol.medicine-list li { margin:0; padding:0; }
        </style>
        ' . $this->patientinfo($consultation) . '
        <ol class="medicine-list">';

        foreach ($prescribe_meds as $medicine) {
            $content .= '<li>'.$medicine->name.'<br><b>(' . ($medicine->brand ?? "") . ')</b><br>Sig: ' . ($medicine->pivot?->remarks ?? "") . ' #' . $medicine->pivot?->quantity . '</li>';
        }

        $content .= '</ol>';
        $this->content = $content;
    }

    protected function generateMedicalCertificateContent($consultation): void
    {
        $patientName = $consultation->patient->full_name;
        $age = Carbon::parse($consultation->patient->birthday)->age;
        $sex = $consultation->patient->sex == 'M' ? 'Male' : 'Female';
        $address = $consultation->patient->address;
        $chiefComplaint = $consultation->chief_complaint ?? '';
        $diagnosis = $consultation->diagnosis ?? 'Diagnosis';
        $dateToday = now()->format('F d, Y');

        // $restStart = Carbon::parse($consultation->estimated_date)->format('F d, Y');
        // $restEnd = Carbon::parse($consultation->estimated_date_to)->addDays(3)->format('F d, Y');
        // $recovery = Number::spell($consultation->approximate_days);
        // $returnDate = Carbon::parse($consultation->created_at)->addDays(4)->format('F d, Y');
        $remarks = $consultation->medical_cert_remarks ?? '___________________________';

        $fontSize = $this->fontSize;
       $content = '
    <div style="font-size:'.$fontSize.'pt; line-height:1.6; font-family: Arial, sans-serif;">
            <div style="margin-top: 40px; text-align:center; font-size: 14pt; font-weight:bold">
                <b>Medical Certificate</b>
            </div>
        <div style="text-align: right; margin-bottom: 20px;">
            Date: <u>'.$dateToday.'</u>
        </div>

       

        <div style="margin-top: 30px; text-align: justify;">To whom it may concern:
            <br><br>This is to certify that <u>'.$patientName.'</u>, <u>'.$age.'</u> years old, <u>'.$sex.'</u>, 
            currently residing at <u>'.$address.'</u>, sought medical consult last
            <u>'.$consultation->date->format('F j, Y').'</u> for medical checkup and assessment:
            <u>'.$diagnosis.'</u>
            <b>Remarks:</b> '.$remarks.'
            <br><br>
        </div>
    </div>';


        $this->content = $content;
    }
}
