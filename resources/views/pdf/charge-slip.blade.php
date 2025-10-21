<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Charge Slip</title>
<meta name="viewport" content="width=device-width,initial-scale=1.0" />
<style>
  :root {
    --border-color: #333;
    --label-font: 14px Arial, sans-serif;
    --value-font: 13px "Courier New", monospace;
  }

  body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
    background: #f7f7f7;
  }

  .slip {
    position: relative;
    max-width: 800px;
    margin: 30px auto;
    background: #fff;
    padding: 20px 60px 20px 40px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    border: 1px solid #ccc;
    page-break-inside: avoid;
  }

  .clinic-name {
    position: absolute;
    top: 20px;
    right: -10px;
    writing-mode: vertical-rl;
    text-orientation: mixed;
    font-size: 16px;
    font-weight: bold;
    letter-spacing: 1px;
    transform: rotate(180deg);
    font-family: "Courier New", monospace;
  }

  .title-row {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
  }

  .title {
    flex: 1;
    font-size: 24px;
    font-weight: bold;
    letter-spacing: 1px;
    margin: 0;
  }

  .charge-box {
    padding: 6px 12px;
    border: 2px solid var(--border-color);
    display: inline-block;
    font-weight: bold;
    letter-spacing: 1px;
    margin-left: 10px;
    font-size: 16px;
  }

  .dashed-sep {
    border-bottom: 2px dashed var(--border-color);
    margin: 10px 0 25px;
  }

  .row {
    display: flex;
    align-items: flex-start;
    gap: 40px;
    margin-bottom: 15px;
  }

  .field {
    display: flex;
    flex-direction: column;
    position: relative;
  }

  .field label {
    font-size: 14px;
    margin-bottom: 4px;
    font-weight: bold;
  }

  .input-line {
    border-bottom: 2px solid var(--border-color);
    padding: 4px 2px;
    min-height: 24px;
    font-family: var(--value-font);
    font-size: 13px;
  }

  .vertical-label {
    writing-mode: vertical-rl;
    text-orientation: mixed;
    font-size: 14px;
    font-weight: bold;
    margin-right: 10px;
    white-space: nowrap;
  }

  .line-group {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  .signature-blocks {
    display: flex;
    gap: 60px;
    margin-top: 10px;
  }

  .signature {
    flex: 1;
    display: flex;
    flex-direction: column;
  }

  .signature label {
    font-weight: bold;
    margin-bottom: 6px;
  }

  .sig-line {
    border-bottom: 2px solid var(--border-color);
    height: 24px;
    width: 100%;
    font-family: var(--value-font);
    padding: 4px 2px;
    font-size: 13px;
  }

  .small-print {
    font-size: 11px;
    margin-top: 25px;
    color: #555;
  }

  .bottom {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-top: 10px;
  }

  .total {
    font-size: 18px;
    font-weight: bold;
  }

  @media print {
    body {
      background: white;
    }
    .slip {
      box-shadow: none;
      border: 1px solid #000;
      margin: 0;
      padding: 10px 40px;
    }
  }
</style>
</head>

<body>
  <div class="slip">
    <div class="clinic-name">
      YBIERNAS EAR NOSE THROAT - HEAD AND NECK SURGERY CLINIC
    </div>

    <div class="title-row">
      <h1 class="title">CHARGE SLIP</h1>
      <div class="charge-box">CHARGE SLIP</div>
    </div>

    <div class="dashed-sep"></div>

    <div class="row">
      <div class="field" style="flex:1;">
        <label>Name:</label>
        <div class="input-line">Juan Dela Cruz</div>
      </div>
      <div class="field">
        <label>Date:</label>
        <div class="input-line">08/02/2025</div>
      </div>
    </div>

    <div class="row">
      <div class="vertical-label">Details</div>
      <div class="line-group">
        <div class="field">
          <label>Consultation:</label>
          <div class="input-line">₱800.00</div>
        </div>
        <div class="field">
          <label>Follow-Up:</label>
          <div class="input-line">₱500.00</div>
        </div>
        <div class="field">
          <label>Procedure:</label>
          <div class="input-line">Endoscopy - ₱3,500.00</div>
        </div>
      </div>
    </div>

    <div class="signature-blocks">
      <div class="signature">
        <label>Discount (Senior/PWD):</label>
        <div class="sig-line">₱400.00</div>
      </div>
      <div class="signature">
        <label>Signature:</label>
        <div class="sig-line">Dr. Ybiernas</div>
      </div>
    </div>

    <div class="bottom">
      <div class="total">
        Total: <span style="border-bottom:2px solid var(--border-color); padding: 0 10px;">₱4,400.00</span>
      </div>
    </div>

    <div class="small-print">
      Please ensure all information is correct before submitting this slip to the cashier.
    </div>
  </div>
  
</body>
</html>
