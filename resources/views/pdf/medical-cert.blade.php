<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Medical Certificate</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <style>
    @page {
      size: letter portrait;
      margin: 2.5cm 2.5cm 0 1.5cm; /* bottom margin = 0 */
    }

    :root {
      --font-stack: "Segoe UI", Arial, sans-serif;
    }

    html, body {
      margin: 0;
      padding: 0;
      font-family: var(--font-stack);
      font-size: 13px;
      line-height: 1.4;
      color: #000;
      background: #fff;
      -webkit-print-color-adjust: exact;
    }

    .certificate {
      min-height: 100vh; /* full page height */
      display: flex;
      flex-direction: column;
      padding: 2rem 2rem 0 2rem; /* no bottom padding */
      box-sizing: border-box;
    }

    .certificate-body {
      flex: 1; /* pushes doctor + footer to the bottom */
    }

    .header {
      text-align: center;
    }

    .header .clinic-name {
      font-weight: 700;
      font-size: 13pt;
    }

    h1 {
      font-size: 18pt;
      margin: 0.5rem 0;
      font-weight: bold;
    }

    .section {
      margin-top: 1rem;
    }

    .patient-info {
      margin-top: 0.5rem;
      line-height: 1.4;
    }

    .doctor-block {
      font-size: 12pt;
      line-height: 1.2;
      text-align: left;
      margin-bottom: 5rem; /* small gap above footer */
    }

    .doctor {
      margin-top: 1rem;
      font-weight: bold;
    }

    .title-small {
      font-size: 12pt;
    }

    .license {
      font-weight: 500;
    }

    .certificate-footer {
      margin-top: auto; /* stick to bottom */
      text-align: center;
      font-size: 11pt;
      color: rgb(71, 71, 71);
    }

    @media print {
      body {
        font-size: 12pt;
      }

      .certificate {
        padding: 1.5rem 2rem 2rem 2rem;
      }
    }
  </style>
</head>
<body>
  <div class="certificate" aria-label="Medical Certificate">
    <div class="certificate-body">
      @include('pdf.header',['title' => 'MEDICAL CERTIFICATE'])

      <div class="section">
        {{-- <p><span>Date:</span> {{ now()->format('F j, Y') }}</p> --}}

        <div class="patient-info">
          This is to certify that <strong>{{ $record->patient->full_name }}</strong>,
          {{ $record->patient->age }} years old,
          {{ $record->patient->sex == 'M' ? 'Male' : 'Female' }},
          from {{ $record->patient->address }},
          was seen at the clinic on {{ $record->date->format('F j, Y') }} and was diagnosed to have: <br>
          <strong>{!! $record->diagnosis !!}</strong>
        </div>

        <p style="margin-top:1rem;">
          {{ $record->patient->sex == 'M' ? 'He' : 'She' }} was treated for such with: 
          <strong>{!!$record->management!!}</strong>
        </p>

        <p style="margin-top:1rem;">
          Medications include the following:
        </p>

        <ul>
          @foreach($record->medicines as $key => $row)
            <li>{{ $row->name }} ({{ $row->brand }}), {{ $row->pivot->remarks }}</li>
          @endforeach
        </ul>

        <div class="section">
          @if($record->approximate_days)
            <p><strong>Advised to rest for:</strong> {{ $record->approximate_days }} days.</p>
          @endif
          @if($record->estimated_date)
            <p><strong>Fit to work on:</strong> {{ $record->estimated_date?->format('F j, Y') }}</p>
          @endif
          @if($record->next_follow_up_schedule)
            <p><strong>Must make a follow-up visit on:</strong> {{ $record->next_follow_up_schedule?->format('F j, Y') }}</p>
          @endif
          @if($record->medical_cert_remarks)
            <p><strong>Others:</strong> {!! preg_replace('#</p>\s*<p>#i', ' ', $record->medical_cert_remarks) !!}</p>
          @endif
        </div>

        <p style="margin-top:1rem;">
          Issued on {{now()->format('F d, Y')}} for whatever legal purpose this may serve {{ $record->patient->sex == 'M' ? 'him' : 'her' }} best.
        </p>
      </div>
    </div>

    <!-- Doctor block above footer -->
    <div class="doctor-block">
      @include('pdf.signatory')
    </div>

    <!-- True footer -->
    <div class="certificate-footer">
      @include('pdf.footer')
    </div>
  </div>

  <script>
    window.addEventListener('load', function () {
      window.print();
    });

    window.addEventListener('afterprint', () => {
      window.close(); // Only works if opened by JS
    });
  </script>
</body>
</html>
