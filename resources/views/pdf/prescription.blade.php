<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Prescription</title>
    <style>
       /* @page {
    size: A5 portrait;
    margin: 10mm;
} */
 @page {
    size: A5;
}

* {
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    font-size: 9pt;
    margin: 0;
    padding: 0;
    background: #f5f5f5; /* Optional light background for screen */
    /* display: flex; */
    justify-content: center;
    padding: 20px;
     display: block; /* ✅ Change this from flex to block */
}

.prescription-container {
   width: 148mm;
height: 210mm;
}
/* .prescription-container {
    width: 148mm;
    height: 210mm;
    background: white;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 10mm;
    page-break-after: always;
    border: 1px solid #ccc; 
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1); 
} */

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
    font-size: 13px;
}
.header img {
    width: 100%;
    height: auto;
}

hr {
    margin: 5px 0 10px;
    border: none;
    border-top: 1px solid #999;
}

.patient-info {
    margin-bottom: 10px;
}

.patient-flex {
    display: flex;
    justify-content: space-between;
    margin-bottom: 5px;
}

.text-right {
    text-align: right;
}

.medicine-info {
    margin-top: 30px;
    margin-bottom: 10px;
}

.medicine-info img {
    width: 50px;
    margin-bottom: 5px;
}

.medicine-list {
    padding-left: 1.2em;
}

.medicine-item {
    margin-bottom: 10px;
}

.medicine-header {
    display: flex;
    justify-content: space-between;
    font-weight: bold;
}

.medicine-brand {
    font-style: italic;
    color: #000;
}

.medicine-sig {
    margin-left: 1em;
}

.page {
    display: flex;
    flex-direction: column;
    height: 100%;
}

.content {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.footer {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-top: 20px;
}

.flex-column {
  display: flex;
  flex-direction: column;
}

.flex-row {
    display: flex;
    flex-direction: row;
}

.signature {
    text-align: left;
    font-size: 8pt;
}
.signature p {
    margin: 2px 0;
}

.underline {
    display: inline-block;
    border-bottom: 1px solid #000;
    min-width: 120px;
}

 .page-footer{
        font-size: 8pt;
    }

@media print {
    html, body {
        width: 148mm;
        height: 210mm;
        margin: 0;
        padding: 0;
        font-size: 9pt;
        background: white;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .prescription-container {
        box-shadow: none;
        border: none;
        page-break-after: always;
    }

    .footer {
        margin-top: auto;
    }
    .page-footer{
        font-size: 8pt;
    }
    .header .clinic-name {
      font-weight: 700;
      font-size: 10pt;
    }
}

    
    </style>
</head>
<body>
    @foreach($medicines as $row)
    <div class="prescription-container">
        <div class="page" style="display: flex; flex-direction: column; height: 100%;">
            @include('pdf.header',['title' => 'PRESCRIPTION'])
    
            <div class="content" style="flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div class="patient-info">
                        <div class="patient-flex">
                            <div><strong>Name:</strong> {{ $patient->full_name }}</div>
                            <div><strong>Date:</strong> {{ now()->format('F j, Y') }}</div>
                        </div>
                        <div class="patient-flex">
                            <div><strong>Address:</strong> {{ $patient->address }}</div>
                            <div>
                                <strong>Age:</strong> {{ \Carbon\Carbon::parse($patient->birthday)->age }} &nbsp;
                                <strong>Sex:</strong> {{ $patient->sex }}
                            </div>
                        </div>
                    </div>
    
                    <div class="medicine-info" style="break-inside: avoid;">
                        <div style="display: flex; align-items: flex-start; gap: 8px;">
                            <img src="{{ asset('images/clinic/rx.png') }}" alt="Rx" style="width: 40px; height: auto; margin-top: 3px;">
                            <div style="flex: 1;">
                                <ol class="medicine-list" style="margin: 0; padding-left: 0; list-style-position: inside;">
                                    @foreach($row as $index => $medicine)
                                    <li class="medicine-item" style="display: flex; align-items: flex-start; margin-bottom: 10px; position: relative;">
                                        <div style="font-weight: bold; margin-right: 6px;">
                                            {{ $loop->iteration }}.
                                        </div>
                                        <div style="flex: 1;">
                                            <div class="medicine-header" style="display: flex; justify-content: space-between; align-items: flex-start;">
                                                <span style="font-weight: bold;">{{ $medicine->name }}</span>
                                                <span class="medicine-qty" 
                                                    style="position: absolute; right: 0; top: 0; font-weight: bold;">
                                                    #{{ $medicine->pivot?->quantity }}
                                                </span>
                                            </div>
                                            @if($medicine->brand)
                                            <div class="medicine-brand">({{ $medicine->brand }})</div>
                                            @endif
                                            <div class="medicine-sig">Sig. {{ $medicine->pivot?->remarks }}</div>
                                        </div>
                                    </li>
                                    @endforeach
                                </ol>

                            </div>
                        </div>
                    </div>


                </div>
    
                <footer class="footer" style="margin-top: 20px;">
                    <div class="flex-column"  style="width: 100%; ">
                        <div class="flex-row" style="width: 100%">
                            <div style="flex: 50%">
                                <div>Next follow-up schedule:</div>
                                <div class="underline">{{ $next_follow_up_schedule }}</div>
                            </div>
                            <div class="signature" style="flex: 50%; padding-left:auto;">
                                @include('pdf.signatory')
                            </div>
                        </div>
                        {{-- <div style="margin-top: 40px" class="page-footer">
                            @include('pdf.footer')
                        </div> --}}
                    </div>
                </footer>
            </div>
        </div>
    </div>
    
    @endforeach

    <script>
        window.addEventListener('load', function () {
            // Safe to run DOM-related or layout-sensitive code here
            window.print();
        });

        window.addEventListener('afterprint', () => {
            console.log('Printing is done!');
            // You can also close the window or redirect here
            // window.close();
             // only works if opened via JS
        });
    </script>
</body>

</html>
