<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Printing PDF...</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
        }

        #message {
            margin-top: 20vh;
            font-family: sans-serif;
            color: #555;
            text-align: center;
        }

        canvas {
            display: block;
        }
    </style>

    <!-- PDF.js UMD build -->
    <script src="{{ asset('js/pdf/pdf.min.js') }}"></script>
</head>
<body>
<div id="message">Loading PDF...</div>

<script>
document.addEventListener('DOMContentLoaded', async function() {

    const paper = "{{ $paper ?? 'letter' }}"; // 'A5' or 'letter'
    const pdfUrl = "{!! route('tcpdf.print.a5', [$id, 'paper' => 'A5', 'type' => $type, 'custom_doc_id' => $custom_doc_id]) !!}";
    const messageEl = document.getElementById('message');

    // PDF.js worker
    pdfjsLib.GlobalWorkerOptions.workerSrc = "{{ asset('js/pdf/pdf.worker.min.js') }}";

    // Set dynamic print CSS
    const style = document.createElement('style');
    style.innerHTML = `
        @media print {
            @page {
                size: A5;
                margin: 10px; /* top/side margin */
            }
            body, html {
                margin: 0;
                padding: 0;
            }
            canvas {
                display: block;
                width: 98%;
                height: auto;
                page-break-after: always;
            }
        }
    `;
    document.head.appendChild(style);

    try {
        const pdf = await pdfjsLib.getDocument(pdfUrl).promise;

        messageEl.style.display = 'none';
        const container = document.createElement('div');
        document.body.appendChild(container);

        // Adjust scale for paper type
        const scale = paper === 'letter' ? 1.2 : 1.5;

        for (let i = 1; i <= pdf.numPages; i++) {
            const page = await pdf.getPage(i);
            const viewport = page.getViewport({ scale });
            const canvas = document.createElement('canvas');
            canvas.width = viewport.width;
            canvas.height = viewport.height;

            // Only add page-break-after for all but last page
            if (i < pdf.numPages) {
                canvas.style.pageBreakAfter = 'always';
            }

            container.appendChild(canvas);
            await page.render({ canvasContext: canvas.getContext('2d'), viewport }).promise;
        }

        // Auto-print + auto-close
        setTimeout(() => {
            window.focus();
            window.print();
            window.onafterprint = () => window.close();
            setTimeout(() => window.close(), 15000); // fallback close
        }, 500);

    } catch (err) {
        console.error('PDF loading failed:', err);
        alert('Failed to load PDF for printing.');
        // window.close();
    }
});
window.addEventListener('afterprint', () => window.close());
</script>
</body>
</html>