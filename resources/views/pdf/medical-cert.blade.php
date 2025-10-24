<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Medical Certificate</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <style>
    /* ===============================
       🧾 PRINT PAGE CONFIG (A5)
    =============================== */
    @page {
      size: A5 portrait;
      margin: 0;
    }

    /* 🧠 General variables */
    :root {
      --font-stack: "Segoe UI", Arial, sans-serif;
    }

    /* ===============================
       🌐 GLOBAL STYLES
    =============================== */
    html, body {
      margin: 0;
      padding: 0;
      font-family: var(--font-stack);
      background-color: #e5e7eb;
      display: flex;
      justify-content: center;
      align-items: flex-start;
      min-height: 100vh;
    }

    /* ===============================
       🧾 PAPER CANVAS SIMULATION
    =============================== */
    .certificate {
      background: white;
      color: #000;
      box-shadow: 0 0 10px rgba(0,0,0,0.3);
      box-sizing: border-box;
      padding: 1.5cm;
      position: relative;
      width: 100%;
    }

    /* ===============================
       ✍️ CONTENT STYLES
    =============================== */
    .header {
      width: 100%;
      height: 159px;
      display: flex;
      justify-content: center;
      align-items: center;
      margin-bottom: 20px !important;
    }

    .header img {
      width: 100%;
      height: 100%;
      object-fit: contain;
      object-position: center;
      border: 3px solid #1e3a8a;
      border-radius: 8px;
    }

    .clinic-name {
      font-weight: 700;
      font-size: 13pt;
    }

    h1 {
      font-size: 16pt;
      margin: 0.5rem 0;
      font-weight: bold;
    }

    .certificate-body {
      flex: 1;
      font-size: 12px;
      line-height: 1.5;
    }

    .section {
      margin-top: 0.8rem;
    }

    .patient-info {
      margin-top: 0.5rem;
      line-height: 1.4;
    }

    .doctor-block {
      font-size: 11pt;
      line-height: 1.3;
      text-align: left;
      margin-top: 2rem;
      margin-bottom: 1rem;
    }

    .certificate-footer {
      text-align: center;
      font-size: 10pt;
      color: rgb(71, 71, 71);
      margin-top: auto;
    }

    .dynamic-underline {
      text-decoration: underline !important;
      text-decoration-color: black !important;
      text-decoration-thickness: 1px !important;
      text-underline-offset: 3px !important;
      color: black !important;
    }

    /* ===============================
       🖨️ PRINT MEDIA (Header & Footer Repeat)
    =============================== */
    @media print {
      @page {
        size: A5 portrait;
        margin: 0;
      }

      body {
        background: none !important;
      }

      .certificate {
        margin: 0;
        box-shadow: none;
        width: 100%;
        padding: 1.5cm;
      }

      /* Enable table layout for repeating header/footer */
      .print-wrapper {
        display: table !important;
        width: 100%;
        border-collapse: collapse !important;
      }

      thead {
        display: table-header-group !important;
      }

      tfoot {
        display: table-footer-group !important;
      }

      .print-body {
        display: table-row-group !important;
      }

      * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        color-adjust: exact !important;
      }
    }
  </style>
</head>
<body>
  <!-- Table structure for repeating header/footer -->
  <table class="print-wrapper">
    <thead>
      <tr>
        <td>
          <div class="header">
            @include('pdf.header', ['title' => 'MEDICAL CERTIFICATE'])
          </div>
        </td>
      </tr>
    </thead>

    <tbody class="print-body">
      <tr>
        <td>
          <div class="certificate" aria-label="Medical Certificate">
            <div class="certificate-body">
              <div class="section patient-info">
                This is to certify that 
                <span class="dynamic-underline"><strong>Test, Test T.</strong></span>,
                10 years old, Male, from 
                <span class="dynamic-underline">Purok 100, Buhangin, Davao City, Philippines</span>,
                was seen at the clinic on 
                <span class="dynamic-underline">October 24, 2025</span> and was diagnosed to have: 
                <br><strong><span>sadasd</span></strong>
              </div>

              <p style="margin-top:1rem;">
                He was treated for such with: 
                <strong>dsadsad</strong>
              </p>

              <p style="margin-top:1rem;">Medications include the following:</p>
              <ul>
                <li>Paracetamol (Biogesic), asdasdad</li>
              </ul>

              <div class="section">
                <p><strong>Others:</strong></p>
                <p>adasdasd adsad sadasdsad asfsafsafasfas</p>
              </div>

              <p style="margin-top:1rem;">
                Issued on October 24, 2025 for whatever legal purpose this may serve him best.
              </p>
              <div class="section">
                <p><strong>Others:</strong></p>
                <p>adasdasd adsad sadasdsad asfsafsafasfas</p>
              </div>

              <p style="margin-top:1rem;">
                Issued on October 24, 2025 for whatever legal purpose this may serve him best.
              </p>
              <div class="section">
                <p><strong>Others:</strong></p>
                <p>adasdasd adsad sadasdsad asfsafsafasfas</p>
              </div>

              <p style="margin-top:1rem;">
                Issued on October 24, 2025 for whatever legal purpose this may serve him best.
              </p>
              <div class="section">
                <p><strong>Others:</strong></p>
                <p>adasdasd adsad sadasdsad asfsafsafasfas</p>
              </div>

              <p style="margin-top:1rem;">
                Issued on October 24, 2025 for whatever legal purpose this may serve him best.
              </p>
              <div class="section">
                <p><strong>Others:</strong></p>
                <p>adasdasd adsad sadasdsad asfsafsafasfas</p>
              </div>

              <p style="margin-top:1rem;">
                Issued on October 24, 2025 for whatever legal purpose this may serve him best.
              </p>
              <div class="section">
                <p><strong>Others:</strong></p>
                <p>adasdasd adsad sadasdsad asfsafsafasfas</p>
              </div>

              <p style="margin-top:1rem;">
                Issued on October 24, 2025 for whatever legal purpose this may serve him best.
              </p>
              <div class="section">
                <p><strong>Others:</strong></p>
                <p>adasdasd adsad sadasdsad asfsafsafasfas</p>
              </div>

              <p style="margin-top:1rem;">
                Issued on October 24, 2025 for whatever legal purpose this may serve him best.
              </p>
              <div class="section">
                <p><strong>Others:</strong></p>
                <p>adasdasd adsad sadasdsad asfsafsafasfas</p>
              </div>

              <p style="margin-top:1rem;">
                Issued on October 24, 2025 for whatever legal purpose this may serve him best.
              </p>
              <div class="section">
                <p><strong>Others:</strong></p>
                <p>adasdasd adsad sadasdsad asfsafsafasfas</p>
              </div>

              <p style="margin-top:1rem;">
                Issued on October 24, 2025 for whatever legal purpose this may serve him best.
              </p>
              <div class="section">
                <p><strong>Others:</strong></p>
                <p>adasdasd adsad sadasdsad asfsafsafasfas</p>
              </div>

              <p style="margin-top:1rem;">
                Issued on October 24, 2025 for whatever legal purpose this may serve him best.
              </p>
              <div class="section">
                <p><strong>Others:</strong></p>
                <p>adasdasd adsad sadasdsad asfsafsafasfas</p>
              </div>

              <p style="margin-top:1rem;">
                Issued on October 24, 2025 for whatever legal purpose this may serve him best.
              </p>
              <div class="section">
                <p><strong>Others:</strong></p>
                <p>adasdasd adsad sadasdsad asfsafsafasfas</p>
              </div>

              <p style="margin-top:1rem;">
                Issued on October 24, 2025 for whatever legal purpose this may serve him best.
              </p>
              <div class="section">
                <p><strong>Others:</strong></p>
                <p>adasdasd adsad sadasdsad asfsafsafasfas</p>
              </div>

              <p style="margin-top:1rem;">
                Issued on October 24, 2025 for whatever legal purpose this may serve him best.
              </p>
              <div class="section">
                <p><strong>Others:</strong></p>
                <p>adasdasd adsad sadasdsad asfsafsafasfas</p>
              </div>

              <p style="margin-top:1rem;">
                Issued on October 24, 2025 for whatever legal purpose this may serve him best.
              </p>
              <div class="section">
                <p><strong>Others:</strong></p>
                <p>adasdasd adsad sadasdsad asfsafsafasfas</p>
              </div>

              <p style="margin-top:1rem;">
                Issued on October 24, 2025 for whatever legal purpose this may serve him best.
              </p>

              <div class="doctor-block">
                @include('pdf.signatory')
              </div>
            </div>
          </div>
        </td>
      </tr>
    </tbody>

    <tfoot>
      <tr>
        <td>
          <div class="certificate-footer">
            @include('pdf.footer')
          </div>
        </td>
      </tr>
    </tfoot>
  </table>

  <script>
    window.addEventListener('load', function () {
      window.print();
    });

    window.addEventListener('afterprint', () => {
      // window.close();
    });
  </script>
</body>
</html>
