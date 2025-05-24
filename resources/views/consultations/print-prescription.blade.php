<div>
    <style></style>
        @page {
            size: A5;
            margin: 20mm;
        }
        body {
            font-family: Arial, sans-serif;
        }
        .prescription {
            width: 100%;
            border: 1px solid #000;
            padding: 10mm;
        }
        .header, .footer {
            text-align: center;
            margin-bottom: 10mm;
        }
        .content {
            margin-bottom: 20mm;
        }
    </style>

    <div class="prescription">
        <div class="header">
            <h1>Clinic Name</h1>
            <p>Address Line 1<br>Address Line 2<br>Phone: (123) 456-7890</p>
        </div>
        <div class="content">
            <h2>Prescription</h2>
            <p><strong>Patient Name:</strong> John Doe</p>
            <p><strong>Date:</strong> {{ date('Y-m-d') }}</p>
            <p><strong>Medication:</strong></p>
            <ul>
                <li>Drug 1 - Dosage</li>
                <li>Drug 2 - Dosage</li>
            </ul>
            <p><strong>Instructions:</strong> Take one tablet daily after meals.</p>
        </div>
        <div class="footer">
            <p>Doctor's Name<br>Signature</p>
        </div>
    </div>
</div>
