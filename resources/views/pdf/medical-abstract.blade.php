<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Medical Abstract</title>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0 20px ;
            font-family: 'Times New Roman', serif;
            font-size: 16px;
            line-height: 1.6;
            display: flex;
            flex-direction: column;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            font-size: 22px;
        }
        .header p {
            margin: 2px 0;
            font-size: 15px;
        }
        .title {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            margin: 30px 0 20px;
        }
        .content {
            text-align: justify;
        }
        .content input {
            border: none;
            border-bottom: 1px solid #000;
            outline: none;
            font-family: inherit;
            font-size: inherit;
            width: 200px;
        }
        main {
            flex: 1;
        }

        .signature {
            margin-top: auto;
        }
        .signature p {
            margin: 4px 0;
        }
        .right {
            text-align: right;
        }
        .header .clinic-name {
            font-weight: 700;
            font-size: 13pt;
            }
    </style>
</head>
<body>
    @php

    $patient_or_parent = $record->patient->age < 18 ? 'Parents were' : 'Patient was';
    @endphp

    @include('pdf.header',['title' => 'MEDICAL ABSTRACT'])

    <main>
    <div class="content">
        <p>
            This is to certify that <b>{{$record->patient_name}}</b>, 
            {{$record->patient->age}} months/years old, {{$record->patient->sex}} from {{$record->patient?->address}}, was seen at the clinic on {{$record->date->format('F j, Y')}} and was diagnosed to have <span style="display:inline">{!!$record->diagnosis!!}</span>.
        </p>

        <p>
            
            {{$patient_or_parent}} informed about the risks and benefits, and the estimated out-of-pocket expense of 
            {{ucwords($record->total_in_words)}}  ({{$record->total}}), more or less.
        </p>

        <p>
            Issued on {{now()->format('F d, Y')}} for whatever legal purpose this may serve the patient best.
        </p>
    </div>
</main>

<div class="signature">
    <p><strong>ANTONIO P. YBIERNAS JR., M.D., COHC</strong></p>
    <p>Otolaryngology - Head and Neck Surgery Specialist</p>
    <p>Lic Number: 0085800</p>
</div>

<div class="certificate-footer">
      @include('pdf.footer')
    </div>


    <script>
        window.addEventListener('load', function () {
            // Safe to run DOM-related or layout-sensitive code here
            window.print();
        });

        window.addEventListener('afterprint', () => {
            console.log('Printing is done!');
            // You can also close the window or redirect here
            window.close(); // only works if opened via JS
        });
    </script>
</body>
</html>
