<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\CustomDoc;
use App\Models\Scopes\TenantScope;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;
use TCPDF;

class CustomTCPDF extends TCPDF
{
    public string $footerHtml = '';
    public int $footerFontSize = 9;

    public function Footer()
    {
        $this->SetY(-32);
        $this->SetFont('helvetica', '', $this->footerFontSize);
        $this->writeHTMLCell(0, 0, '', '', $this->footerHtml, 0, 1, 0, true, 'L', true);
    }
}

class AFivePaperController extends Controller
{
    public $image_header = '';
    protected $content = '';
    protected int $fontSize;

    public function __construct()
    {
        $this->fontSize = request('paper') == 'A5' ? 8 : 10;
    }

    public function getHeader(): string
    {
        return '<br>';
        // return '<img src="' . $this->image_header . '" alt="Header Image" style="width:100%;">';
        return '
            <div style="text-align:center; line-height:1.1;">
                <b>BEN JAY PORCADILLA, RMT, MD, FPCP</b><br>
                <span>Internal Medicine - Adult Disease Specialist</span>
            </div>
            <hr style="margin:2px 0;">
            <table cellpadding="1" style="font-size:11px;">
                <tr>
                    <td width="50%">
                        <b>Davao Medical School Foundation Hospital, Inc.</b><br>
                        Davao City, Davao del Sur<br>
                        Admissions Only<br>
                        09457095110
                    </td>
                    <td width="50%">
                        <b>Medical Mission Group of Hospitals - Tagum</b><br>
                        Tagum City, Davao del Norte<br>
                        Admissions Only<br>
                        09457095110
                    </td>
                </tr>
                <tr>
                    <td width="50%">
                        <b>St. Camillus Hospital of Mati Foundation, Inc.</b><br>
                        Mati City, Davao Oriental<br>
                        Admissions Only<br>
                        09457095110
                    </td>
                    <td width="50%">
                        <b>Davao de Oro Provincial Hospital - Montevista</b><br>
                        Montevista, Davao de Oro<br>
                        Admissions Only<br>
                        09457095110
                    </td>
                </tr>
            </table>
            <hr style="margin:2px 0;">
        ';
    }

    public function getFooter($isPrescription = false, $nextFollowUp = null, $consultation = null): string
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
        <table width="100%" style="font-size: '.$fontSize.'; line-height:1.2;">
            <tr>
                <!-- Left side -->
                <td width="50%" style="vertical-align:bottom; text-align:left;">' . ($isPrescription ? '<b>Next Follow-up Schedule:</b>
                <br><br><u>'.$nextFollowUp.'</u>' : '') . '
                </td>

                <!-- Spacer -->
                <td width="15%"></td>

                <!-- Right side -->
                <td width="35%" style="text-align:left; vertical-align:bottom">
                    <b>Attending Physician:</b><br><br>
                    <b>'.$doctorName.'</b><br>
                    License No.: '.$licenseNo.'<br>
                    PTR No.: '.$ptrNo.'<br>
                    S2 License No.: '.$s2LicenseNo.'
                </td>
            </tr>
        </table>
    ';
    }


    public function generate(Request $request, $id)
    {
        // Check first if the record is from custom Docs

        if($request->custom_doc_id) {
            $custom_doc = CustomDoc::with([
                                'consultation' => fn($query) => $query->withoutGlobalScope(TenantScope::class)->with('doctor')
                            ])
                            ->find($request->custom_doc_id);

            $consultation = $custom_doc->consultation;

        } else {

            $consultation = Consultation::withoutGlobalScope(TenantScope::class)->with(['patient', 'medicines', 'doctor'])->findOrFail($id);

        }

        // Create PDF document (A5)

        $paper = request('paper') ?? 'letter';

        // Use clinic's header image for prescriptions, or medcert header for Medical Certificate
        if ($request->type === 'Medical Certificate') {
            $this->image_header = public_path('storage/' . $consultation->clinic->medcert_header_image);
        } else {
            // Use clinic's prescription header image (dynamic height based on actual image)
            $this->image_header = $consultation->clinic->header_image
                ? public_path('storage/' . $consultation->clinic->header_image)
                : public_path('images/prescription_header.png'); // fallback to default
        }
        $pdf = new CustomTCPDF('P', 'mm', $paper, true, 'UTF-8', false);

        // Document info
        $pdf->SetCreator('TCPDF');
        $pdf->SetAuthor('Dr. Ben Jay Porcadilla');
        $pdf->SetTitle(request('type') ?? 'Prescription');

        // Disable TCPDF's default header, enable footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(true);

        $nextFollowUp = $consultation->next_follow_up_schedule ? Carbon::parse($consultation->next_follow_up_schedule)->format('F j, Y') : null;
        // Set footer content
        $pdf->footerHtml = $this->getFooter(isPrescription: !$request->type, nextFollowUp: $nextFollowUp, consultation: $consultation);
        $pdf->footerFontSize = $this->fontSize;

        // Margins and auto-break (bottom margin for footer)
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 35);
        $pdf->SetFont('helvetica', '', $this->fontSize);

        // ---------- PAGE ADDER FUNCTION ----------
        $isMedcert = $request->type === 'Medical Certificate';
        $addPage = function ($content, $showHeader = true) use ($pdf, $isMedcert) {
            $pdf->AddPage();

            // 🟩 HEADER IMAGE HANDLING
            if ($showHeader && file_exists($this->image_header)) {
                $pageWidth = $pdf->getPageWidth();

                // Reduce size a bit — use 90% of full width
                $imgWidth = $pageWidth * 0.9;

                // Center the image horizontally
                $x = ($pageWidth - $imgWidth) / 2;

                // Top margin
                $imgY = 5;

                // Draw image (auto height)
                $pdf->Image(
                    $this->image_header,
                    $x,
                    $imgY,
                    $imgWidth,
                    0, // keep aspect ratio
                    '',
                    '',
                    '',
                    false,
                    300,
                    '',
                    false,
                    false,
                    0,
                    false,
                    false,
                    false
                );

                // Dynamically move cursor below image
                list($origW, $origH) = getimagesize($this->image_header);
                $aspectRatio = $origH / $origW;
                $imgHeight = $imgWidth * $aspectRatio;
                $headerSpacing = $isMedcert ? 5 : -2;
                $pdf->SetY($imgY + $imgHeight + $headerSpacing);
            } else {
                $pdf->SetY(20);
            }

            // 🟨 MAIN CONTENT
            $pdf->writeHTML($content, true, false, true, false, '');
        };

        // Patient info


        // ---------- CONTENT ----------
        // Split medicines into chunks of 5 per page
        if (! $request->type) {
            $chunks = $consultation->medicines->chunk(5);
            $pageIndex = 0;
            $totalChunks = count($chunks);

            foreach ($chunks as $chunk) {
                $pageIndex++;
                // $isFirstPage = $pageIndex === 1;
                // $isLastPage = $pageIndex === $totalChunks;
                // Generate content for this page
                $this->generatePrescriptionContent($consultation, $chunk);

                $content = $this->content;

                // Add page
                $addPage($content, true);
            }
        } else {

            $custom_content = '';
            if (! $request->custom_doc_id) {

               if ($request->type == 'Admitting Orders') {

                    $custom_content = $consultation->admitting_order_data;

                } else if($request->type == 'Laboratory Request') {

                    $custom_content = $consultation->lab_request_content;
                    
                } else if($request->type == 'Referral Form') {
                    
                    $custom_content = $consultation->referral_content;

                } else if($request->type == 'Medical Abstract') {
                    
                    $custom_content = $consultation->medical_abstract;
                }
            }
             else {

                $custom_content = $custom_doc->content;
                
            }

            if($request->type == 'Laboratory Request') {
            // dd($consultation->labRequests);
            $consultation->labRequests->each(function($item) use($request, $consultation, $addPage) {
                
                $this->generateCustomContent($request->type, $item->content, $consultation);
                $addPage($this->content, true);
            });

        } else {
            $this->generateCustomContent($request->type, $custom_content, $consultation);
            $contentParts = explode('<!--pagebreak-->', $this->content);

            foreach ($contentParts as $index => $part) {
                $isFirstPage = $index === 0;
                $addPage($part, $isFirstPage);
            }
        }
        }



        // $page1 .= $consultation->admitting_order_data;


        // $page2 = '<p style="font-size:11px;">Second page content...</p>';
        // $page3 = '<p style="font-size:11px;">Third page content...</p>';

        // ---------- BUILD PDF ----------
        // $addPage($page1,  true, true); // First page with header
        // $addPage($page2);
        // $addPage($page3, false, true); // Last page with footer

        // ---------- OUTPUT ----------
        $pdf->Output('prescription.pdf', 'I');
    }

    protected function patientinfo($consultation)
    {
        $sex = $consultation->patient->sex == 'M' ? 'Male' : 'Female';
        $age = Carbon::parse($consultation->patient->birthday)->age;
        $name = $consultation->patient->full_name;
        $address = $consultation->patient->address;
        $date = now()->format('F j, Y');
        $fontSize = $this->fontSize;
        $content = '<style>
            .info-table {
                width: 100%;
                font-size: '.$fontSize.';
                border-collapse: collapse; 
                padding:0; margin:0;
                
            }
            .info-table td {
                vertical-align: top;
                padding: 1px 0;
                line-height:1.5;
            }
            .right { text-align: right; }
        </style>';

        $content .= '<table class="info-table" >
            <tr>
                <td><b>Name:</b> ' . $name . '</td>
                <td class="right"><b>Date:</b> ' . $date . '</td>
            </tr>
            <tr>
                <td><b>Address:</b> ' . $address . '</td>
                <td style="text-align:right"><b>Age:</b> ' . $age . ' &nbsp;&nbsp;&nbsp;&nbsp;<b>Sex:</b> ' . $sex . '</td>
            </tr>
           
            ';
            if (! request('type')) {
                
            } else {
                $content .= '
                <tr>
                    <td colspan="2" style="line-height:1;">
                        <div style="text-align:center; font-weight:bold; font-size: '.($fontSize+3).'; text-transform:uppercase">'.(request('type') ?? '').'</div>
                    </td>
                </tr>';
                
            }
            
        $content .= '</table>';

        return $content;
    }

    // content generation

    protected function generateCustomContent($title, $content, $consultation): void
    {
        $final_content = $this->patientinfo($consultation);
        // Convert newlines to <br> tags for plain text content
        $content = nl2br(htmlspecialchars($content));
        $final_content .= '<div style="font-size:' . $this->fontSize . 'pt; line-height:1.4;"><br><br>' . $content . '</div>';
        $this->content = $final_content;
    }


    protected function generatePrescriptionContent($consultation, $prescribe_meds): void
    {
        $fontSize = $this->fontSize; // 9pt for A5, 12pt for letter

        $content = '
        <style>
            .info-table {
                width: 100%;
                font-size: ' . $fontSize . 'pt;
                border-collapse: collapse;
            }
            .info-table td {
                vertical-align: top;
                padding: 1px 0;
            }
            .right { text-align: right; }

            ol.medicine-list {
                list-style-type: decimal;
                margin: 0;
                padding-left: 15px;
                font-size: ' . $fontSize . 'pt;
            }
            ol.medicine-list li {
                line-height: 1.3;
                margin-bottom: 8px;
                padding-bottom: 4px;
            }
        </style>

        ' . $this->patientinfo($consultation) . '

        <!-- Add vertical spacing between patient info and medicines -->
        <div style="height:10px;"></div>

        <table width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <!-- RX Label Column -->
                <td width="8%" style="vertical-align:top; padding-top:10px;">
                    <img src="' . public_path('images/clinic/rx.png') . '"
                         style="width:35px; height:auto; display:block; margin-top:2px;">
                </td>

                <!-- Medicines Column -->
                <td width="92%" style="vertical-align:top; padding-top:3px;">
                    <ol class="medicine-list" style="margin-top:0;">';

        foreach ($prescribe_meds as $medicine) {
            $content .= '
                        <li>
                            <table width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <!-- Medicine name + brand -->
                                    <td width="80%" style="vertical-align:top; padding-bottom:2px; font-size:' . $fontSize . 'pt; line-height:1.2;">
                                        ' . htmlspecialchars($medicine->name) . '<br>
                                        <b>(' . htmlspecialchars($medicine->brand ?? "") . ')</b><br>
                                        Sig: ' . htmlspecialchars($medicine->pivot?->remarks ?? "") . '
                                    </td>

                                    <!-- Quantity aligned right -->
                                    <td width="20%" style="text-align:right; vertical-align:top; font-size:' . $fontSize . 'pt;">
                                        <b>#' . htmlspecialchars($medicine->pivot?->quantity ?? "") . '</b>
                                    </td>
                                </tr>
                            </table>
                        </li>';
        }

        $content .= '
                    </ol>
                </td>
            </tr>
        </table>';

        $this->content = $content;
    }





    
}
