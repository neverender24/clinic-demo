<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Charge Slip</title>
<meta name="viewport" content="width=device-width,initial-scale=1.0" />
<style>
  :root {
    --dark:#222;
    --line:#222;
    --label-font:13px "Helvetica", Arial, sans-serif;
    --value-font:12px "Courier New", monospace;
  }
  * {box-sizing:border-box;}
  body {
    margin:0;
    background:#e8e8e8;
    font-family: "Helvetica", Arial, sans-serif;
    padding:0;
  }
  .slip {
    position: relative;
    /* half A4 crosswise: 210mm x 148.5mm, landscape */
    width: 210mm;
    height: 148.5mm;
    margin: 10mm auto;
    background:#fff;
    padding: 12mm 12mm 10mm 12mm;
    border:1px solid #555;
    font-size:12px;
    color:#111;
    line-height:1.1;
    overflow:hidden;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }
  .clinic-header {
    text-align: center;
    margin-bottom: 4px;
  }
  .clinic-name {
    font-size: 14px;
    font-weight: bold;
    letter-spacing: 1px;
    margin: 0;
    line-height:1.1;
  }
  .clinic-sub {
    font-size: 11px;
    margin: 0;
    margin-top: 2px;
  }
  .header {
    display:flex;
    align-items:center;
    gap:8px;
    margin: 4px 0 6px;
  }
  .title {
    font-size:22px;
    font-weight:700;
    margin:0;
    letter-spacing:1px;
    text-align: center;
    flex:1;
  }
  .stamp {
    border:2px solid var(--dark);
    padding:4px 16px;
    font-weight:700;
    font-size:14px;
    letter-spacing:1px;
    display:inline-block;
    font-family:"Courier New", monospace;
    position: relative;
  }
  .dashed {
    margin:6px 0 14px;
    border-bottom:2px dashed var(--line);
    width:100%;
  }
  .row {
    display:flex;
    gap:12px;
    margin-bottom:10px;
  }
  .field {
    flex:1;
    display:flex;
    flex-direction:column;
    position:relative;
  }
  .field label {
    font-size:11px;
    font-weight:600;
    margin-bottom:3px;
  }
  .line {
    border-bottom:1.8px solid var(--line);
    min-height:18px;
    padding:2px 5px;
    font-family: var(--value-font);
    font-size:12px;
    position:relative;
    white-space:nowrap;
  }
  .details {
    display:flex;
    gap:12px;
    margin-bottom:10px;
  }
  .vertical-placeholder {
    width:8px;
  }
  .group {
    flex:1;
    display:flex;
    flex-direction:column;
    gap:6px;
  }
  .signature-row {
    display:flex;
    gap:40px;
    margin-top:4px;
  }
  .sig-block {
    flex:1;
    display:flex;
    flex-direction:column;
  }
  .sig-block label {
    font-weight:600;
    margin-bottom:4px;
    font-size:11px;
  }
  .sig-line {
    border-bottom:1.8px solid var(--line);
    height:20px;
    position:relative;
    padding:0 4px;
    font-family: "Brush Script MT", cursive;
    font-size:14px;
    display:flex;
    align-items:center;
  }
  .total-area {
    display:flex;
    justify-content: flex-start;
    align-items:center;
    margin-top:6px;
    gap:8px;
  }
  .total {
    font-size:16px;
    font-weight:700;
  }
  .total-box {
    border-bottom:1.8px solid var(--line);
    padding:2px 10px;
    min-width:100px;
    display:inline-block;
    font-family: var(--value-font);
  }
  .small {
    font-size:9px;
    margin-top:6px;
    color:#444;
  }

  /* print page sizing */
  @page {
    size: 210mm 148.5mm;
    margin: 0;
  }
  @media print {
    body {
      background: white;
    }
    .slip {
      margin:0;
      border:1px solid #000;
      box-shadow:none;
      page-break-inside: avoid;
    }
  }
</style>
</head>
<body>
  <div class="slip">
    <div>
      <div class="clinic-header">
        <p class="clinic-name">YBIERNAS EAR NOSE THROAT</p>
        <p class="clinic-sub">HEAD AND NECK SURGERY CLINIC</p>
      </div>

      <div class="header">
        <h1 class="title">CHARGE SLIP</h1>
      </div>

      <div class="dashed"></div>

      <div class="row">
        <div class="field">
          <label>Name:</label>
          <div class="line">{{$record->patient?->full_name}}</div>
        </div>
        <div class="field" style="flex:0 0 110px;">
          <label>Date:</label>
          <div class="line">{{$record->date->format('m/d/Y')}}</div>
        </div>
      </div>

      <div class="details">
        <div class="vertical-placeholder"></div>
        <div class="group">
          <div class="field">
            <label>Consultation:</label>
            <div class="line">{{$record->fee}}</div>
          </div>
          <div class="field">
            <label>Follow-Up:</label>
            <div class="line">{{$record->follow_up_fees}}</div>
          </div>
          <div class="field">
            <label>Procedure:</label>
            <div class="line">-</div>
          </div>
        </div>
      </div>

      <div class="signature-row">
        <div class="sig-block">
          <label>Discount (Senior/PWD):</label>
          <div class="line">{{$record->discount}}%</div>
        </div>
        <div class="sig-block">
          <label>Signature:</label>
          <div class="sig-line">&nbsp;</div>
          {{-- <div class="sig-line">Dr. Ybiernas</div> --}}
        </div>
      </div>
    </div>

    <div>
      <div class="total-area">
        <div class="total">Total:</div>
        <div class="total-box">{{$record->total}}</div>
      </div>
      <div class="small">
        Please ensure all information is complete. This slip is required for processing and payment.
      </div>
    </div>
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
