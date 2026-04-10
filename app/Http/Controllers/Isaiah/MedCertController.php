<?php

namespace App\Http\Controllers\Isaiah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Scopes\TenantScope;
use App\Support\PrintableContent;
use Illuminate\Support\Number;
use TCPDF;
use App\Models\Consultation;
use App\Models\ClinicSetting;
use Filament\Forms\Components\RichEditor\RichContentRenderer;

class MedCertController extends Controller
{
    public $image_header = '';
    protected $content = '';
    protected int $fontSize;
    protected array $medCertSettings = [];

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
    public function getFooter($isPrescription = false, $consultation = null): string
    {
        $fontSize = $this->fontSize;

        // Get doctor info from consultation
        $doctorName = '________________________';
        $licenseNo = '________________________';
        $ptrNo = '________________________';
        $s2LicenseNo = '________________________';

        if ($consultation && $consultation->doctor) {
            $doctor = $consultation->doctor;
            $doctorName = $doctor->name ?? $doctorName;
            $licenseNo = $doctor->license_no ?? $licenseNo;
            $ptrNo = $doctor->ptr_no ?? $ptrNo;
            $s2LicenseNo = $doctor->s2_license_no ?? $s2LicenseNo;
        }

        return '
        <table width="100%" style="font-size:'.$fontSize.'pt; line-height:1.2;">
            <tr>
                <td width="55%" style="vertical-align:bottom; text-align:left;">
                    ' . ($isPrescription ? '<b>Next Follow-up Schedule:</b> ________________________' : '') . '
                </td>
                <td width="10%"></td>
                <td width="35%" style="text-align:left; vertical-align:bottom;">
                    <b>Attending Physician:</b><br><br>
                    <b>'.$doctorName.'</b><br>
                    License No.: '.$licenseNo.'<br>
                    PTR No.: '.$ptrNo.'<br>
                    S2 License No.: '.$s2LicenseNo.'
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
            ->with(['patient', 'medicines', 'doctor'])
            ->findOrFail($id);

        $medCertSettings = ClinicSetting::getMedCertSettings($consultation->clinic_id);
        $this->medCertSettings = $medCertSettings;
        $paper = strtolower($medCertSettings['paper_size'] ?? $request->paper ?? 'letter');
        $this->fontSize = match ($paper) {
            'a5'    => 8,
            'a4'    => 11,
            'legal' => 12,
            default => 11, // letter
        };
        $clinicId = $consultation->clinic_id;

        // Check for medcert-specific header from settings (uploaded via FileUpload)
        $headerImage = $medCertSettings['header_image'] ?? null;
        if ($headerImage) {
            $this->image_header = storage_path('app/public/' . $headerImage);
        } else {
            // Fall back to header maker's certificate header, then prescription header
            $certHeader = public_path("images/clinic_{$clinicId}/certificate_header.png");
            $prescriptionHeader = public_path("images/clinic_{$clinicId}/prescription_header.png");
            $defaultHeader = public_path('images/prescription_header.png');

            if (file_exists($certHeader)) {
                $this->image_header = $certHeader;
            } elseif (file_exists($prescriptionHeader)) {
                $this->image_header = $prescriptionHeader;
            } else {
                $this->image_header = $defaultHeader;
            }
        }

        $pdf = $this->setupPdf($paper);
        $headerHtml = $this->getHeader();
        $footerHtml = $this->getFooter(isPrescription: !$request->type, consultation: $consultation);

        $this->generateMedicalCertificateContent($consultation, $medCertSettings);

        $this->addCustomPage($pdf, $this->content, $headerHtml, $footerHtml, true, true, $paper);

        $pdf->Output('prescription.pdf', 'I');
    }

    /** ------------------------------
     *  PAGE SETUP SEPARATION
     *  ------------------------------ */
    protected function getFooterHeight(string $paper): int
    {
        return match ($paper) {
            'a5'     => 45,
            'legal'  => 52,
            'a4'     => 55,
            default  => 55, // letter
        };
    }

    protected function setupPdf(string $paper): TCPDF
    {
        $pdf = new TCPDF('P', 'mm', strtoupper($paper), true, 'UTF-8', false);
        $pdf->SetCreator('TCPDF');
        $pdf->SetAuthor('Dr. Ben Jay Porcadilla');
        $pdf->SetTitle(request('type') ?? 'Prescription');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $margins = match ($paper) {
            'a5'     => ['left' => 10, 'top' => 10, 'right' => 10],
            'a4'     => ['left' => 15, 'top' => 15, 'right' => 15],
            'legal'  => ['left' => 10, 'top' => 15, 'right' => 10],
            default  => ['left' => 15, 'top' => 15, 'right' => 15], // letter
        };

        $pdf->SetMargins($margins['left'], $margins['top'], $margins['right']);
        // Use a small bottom margin for content pages; footer space is handled manually on the last page
        $pdf->SetAutoPageBreak(true, 10);
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

            $imgY        = (float) ($this->medCertSettings['header_margin_top'] ?? 5);
            $widthPct    = (float) ($this->medCertSettings['header_width_percent'] ?? 100);
            $spacing     = (float) ($this->medCertSettings['header_spacing'] ?? 3);

            $imgWidth = $usableWidth * ($widthPct / 100);
            $imgX     = $leftMargin + ($usableWidth - $imgWidth) / 2;

            $pdf->Image(
                $this->image_header,
                $imgX,
                $imgY,
                $imgWidth,
                0,
                '',
                '',
                '',
                false,
                300
            );

            // Dynamically calculate image height and position cursor below it
            list($origW, $origH) = getimagesize($this->image_header);
            $aspectRatio = $origH / $origW;
            $imgHeight   = $imgWidth * $aspectRatio;
            $pdf->SetY($imgY + $imgHeight + $spacing);
        } else {
            $pdf->SetY(20);
        }

        // --- CONTENT ---
        $pdf->writeHTML($content, true, false, true, false, '');

        // --- FOOTER (last page only, fixed at bottom) ---
        if ($isLastPage) {
            $footerHeight = $this->getFooterHeight($paper);
            $pageHeight = $pdf->getPageHeight();
            $footerY = $pageHeight - $footerHeight;

            // Go to the last page
            $pdf->setPage($pdf->getNumPages());
            $currentY = $pdf->GetY();

            // If content overlaps the footer zone, add a new page
            if ($currentY > $footerY) {
                $pdf->AddPage();
            }

            // Place footer at fixed bottom position
            $pdf->SetAutoPageBreak(false);
            $pdf->SetY($footerY);
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

    protected function generateMedicalCertificateContent($consultation, array $medCertSettings = []): void
    {
        $patientName = $consultation->patient->full_name;
        $age = Carbon::parse($consultation->patient->birthday)->age;
        $sex = $consultation->patient->sex == 'M' ? 'Male' : 'Female';
        $address = $consultation->patient->address;
        $diagnosis = $this->renderPlainTextForPrint($consultation->diagnosis ?? 'Diagnosis');
        $management = $this->renderPlainTextForPrint($consultation->management ?? '___________________________');
        $dateToday = now()->format('F d, Y');
        $remarks = $consultation->medical_cert_remarks
            ? $this->renderPlainTextForPrint($consultation->medical_cert_remarks)
            : '___________________________';
        $fontSize = $this->fontSize;

        $consultationDate = $consultation->created_at
            ? Carbon::parse($consultation->created_at)->format('F d, Y')
            : $dateToday;

        $estimatedDate = $consultation->estimated_date
            ? Carbon::parse($consultation->estimated_date)->format('F d, Y')
            : '___________________________';
        $estimatedDateTo = $consultation->estimated_date_to
            ? Carbon::parse($consultation->estimated_date_to)->format('F d, Y')
            : '___________________________';
        $approximateDays = $consultation->approximate_days
            ? Number::spell($consultation->approximate_days) . ' (' . $consultation->approximate_days . ')'
            : '___________________________';
        $returnDate = $consultation->return_date
            ? Carbon::parse($consultation->return_date)->format('F d, Y')
            : '___________________________';

        $mergeTagValues = [
            'name' => $patientName,
            'age' => (string) $age,
            'sex' => $sex,
            'address' => $address,
            'date' => $dateToday,
            'consultation_date' => $consultationDate,
            'diagnosis' => $diagnosis,
            'management' => $management,
            'remarks' => $remarks,
            'estimated_date' => $estimatedDate,
            'estimated_date_to' => $estimatedDateTo,
            'approximate_days' => (string) $approximateDays,
            'return_date' => $returnDate,
            'chief_complaint' => $this->renderPlainTextForPrint($consultation->chief_complaint ?? '___________________________'),
        ];

        $headerMergeTags = [
            '{{ name }}' => $patientName,
            '{{ age }}' => $age,
            '{{ sex }}' => $sex,
            '{{ address }}' => $address,
            '{{ date }}' => $dateToday,
            '{{ consultation_date }}' => $consultationDate,
        ];

        $content = '';

        // Patient details header
        $withHeader = $medCertSettings['with_header'] ?? true;
        $headerFields = $medCertSettings['header_fields'] ?? ['name', 'date', 'age', 'address', 'sex'];

        if ($withHeader && !empty($headerFields)) {
            $content .= $this->buildPatientHeader($headerFields, $headerMergeTags, $fontSize);
        }

        // Body content from settings template
        $templateContent = $medCertSettings['content'] ?? null;

        if ($templateContent) {
            if (is_array($templateContent)) {
                // JSON content from RichEditor with .json()
                // First try RichContentRenderer for proper mergeTag nodes
                $body = RichContentRenderer::make($templateContent)
                    ->mergeTags($mergeTagValues)
                    ->toUnsafeHtml();
            } else {
                $body = $templateContent;
            }

            // Also replace any text-based {{ tag }} patterns in the rendered HTML
            $htmlMergeTags = [];
            foreach ($mergeTagValues as $key => $value) {
                $htmlMergeTags['{{ '.$key.' }}'] = $value;
            }
            $body = str_replace(array_keys($htmlMergeTags), array_values($htmlMergeTags), $body);

            $content .= '<style>p { margin:0; line-height:1.3; }</style>';
            $content .= '<div style="font-size:'.$fontSize.'pt; line-height:1.3; font-family: Arial, sans-serif;">'.$body.'</div>';
        } else {
            // Fallback to original hardcoded content
            $content .= '
            <div style="font-size:'.$fontSize.'pt; line-height:1.3; font-family: Arial, sans-serif;">
                <div style="text-align:center; font-size: 14pt; font-weight:bold">
                    <b>Medical Certificate</b>
                </div>
                <div style="text-align: right; margin-bottom: 20px;">
                    Date: <u>'.$dateToday.'</u>
                </div>
                <div style="margin-top: 30px; text-align: justify;">To whom it may concern:
                    <br><br>This is to certify that <u>'.$patientName.'</u>, <u>'.$age.'</u> years old, <u>'.$sex.'</u>,
                    currently residing at <u>'.$address.'</u>, sought medical consult for medical checkup and assessment:
                    <br><u>'.$diagnosis.'</u><br>
                    <br><b>Remarks:</b> '.$remarks.'
                    <br><br>
                </div>
            </div>';
        }

        $this->content = $content;
    }

    protected function buildPatientHeader(array $fields, array $mergeTags, int $fontSize): string
    {
        $leftParts = [];
        $rightParts = [];

        if (in_array('name', $fields)) {
            $leftParts[] = '<b>Name:</b> '.$mergeTags['{{ name }}'];
        }
        if (in_array('date', $fields)) {
            $rightParts[] = '<b>Date:</b> '.$mergeTags['{{ date }}'];
        }
        if (in_array('address', $fields)) {
            $leftParts[] = '<b>Address:</b> '.$mergeTags['{{ address }}'];
        }
        if (in_array('age', $fields)) {
            $rightParts[] = '<b>Age:</b> '.$mergeTags['{{ age }}'];
        }
        if (in_array('sex', $fields)) {
            $rightParts[] = '<b>Sex:</b> '.$mergeTags['{{ sex }}'];
        }
        if (in_array('consultation_date', $fields)) {
            $rightParts[] = '<b>Date of Consultation:</b> '.$mergeTags['{{ consultation_date }}'];
        }

        $html = '<table width="100%" style="font-size:'.$fontSize.'pt; border-collapse:collapse;">';

        $maxRows = max(count($leftParts), count($rightParts));
        for ($i = 0; $i < $maxRows; $i++) {
            $left = $leftParts[$i] ?? '';
            $right = $rightParts[$i] ?? '';
            $html .= '<tr><td>'.$left.'</td><td style="text-align:right;">'.$right.'</td></tr>';
        }

        $html .= '</table><br>';

        return $html;
    }

    protected function renderPlainTextForPrint(mixed $content): string
    {
        $text = PrintableContent::toPlainText($content);

        return $text === '' ? '' : nl2br(e($text), false);
    }
}
