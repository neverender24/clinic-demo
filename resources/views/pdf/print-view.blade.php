<!DOCTYPE html>
<html>
<head>
    <title>Print Prescription</title>
</head>
<body>
    <iframe id="pdf-frame" style="width:0; height:0; border:0;" hidden></iframe>

    <script>
        const iframe = document.getElementById('pdf-frame');

        fetch("{{ route('prescription.pdf', ['id' => $record->id]) }}")
            .then(response => response.json())
            .then(data => {
                iframe.src = data.url;

                iframe.onload = () => {
                    setTimeout(() => {
                        const childWindow = iframe.contentWindow;

                        childWindow.focus();
                        childWindow.print();

                        // Close the current tab after printing
                        window.onafterprint = () => {
                            window.close();
                        };
                    }, 300);
                };
            })
            .catch(error => {
                console.error('Failed to load PDF:', error);
                window.close(); // Fallback if loading fails
            });

            window.addEventListener('afterprint', () => {
                console.log('Printing is done!');
                // You can also close the window or redirect here
                window.close(); // only works if opened via JS
            });
    </script>
</body>
</html>
