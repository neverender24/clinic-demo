<style>
    @page {
        size: A5;
    }
    body {
        font-family: Arial, sans-serif;
    }
/*
        .prescription {
            width: 100%;
            border: 1px solid #000;
            padding: 10mm;
        } */
        /* .header, .footer {
            text-align: center;
            margin-bottom: 10mm;
        } */
        /* .content {
            margin-bottom: 20mm;
        } */

   .prescription-container {
        width: 148mm;
        height: 210mm;
        /* background-color: white;
        width: 556.8px;
        */
        /* width: 556.8px;   */
        /* Custom width */
        /* height: 797px;  */
        /* height: 796.8000000000001px;  */
        /* Custom height */
        /* padding: 20px; */

        /* Push footer to the bottom */
        /* justify-content: space-between;  */
        /* overflow: hidden; */
    }
    /* .physician-signature p {
        margin: 5px 0;
        font-size: 9pt;
    } */
    @media print {
        body {
            size: A5;
            margin: 20mm;
            width: 148mm;
            height: 210mm;
        }
        /* .prescription-container {
            width: 556.8px;
            height: 797px;
            page-break-after: always;
            margin: 1in;
        } */
    }


</style>
<div class="prescription-container bg-white shadow flex flex-col text-[9pt] p-[10mm]" id="prescription">
        <!-- Heading Section -->
        <header class="header">
            <img src="{{asset('storage/'.$header_image)}}" alt="Clinic Logo" class="logo">
            <h1>
                <hr>
            </h1>
        </header>

        <section class="content pt-2`">
          {!! $data !!}
        </section>
        <footer class="footer h-full flex items-end">
            <div class="grid grid-cols-6 gap-x-5 mt-auto w-full">
                <div class="col-span-3 flex flex-col justify-between pb-2">
                    <div>
                    <!-- Next follow-up schedule: -->
                    </div>
                    <div class="b mt-auto">
                    </div>
                </div>
                <div class="col-span-3 physician-signature gap-y-0 text-[7pt] flex justify-end">
                   <div>
                     <p>BEN JAY C. PORCADILLA, RMT, MD, FPCP</p>
                     <p>License no: 0132066</p>
                     <p>PTR no: 2173419</p>
                     <p>S2 License no: _____________________</p>
                   </div>
                </div>
            </div>
        </footer>

        <!-- Footer Section -->
    </div>
