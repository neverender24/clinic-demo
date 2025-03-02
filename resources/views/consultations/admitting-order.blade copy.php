<style>
   .prescription-container {
        /* background-color: white;
        width: 556.8px;   */
        /* Custom width */
        height: 797px; 
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
    
    
</style>

<div class="prescription-container bg-white shadow pl-14 pr-10 gap-x-0 flex flex-col text-[12pt] py-4" id="admitting-order">
    <!-- Heading Section -->
    <header class="header">
        <img src="{{asset('storage/'.$header_image)}}" alt="Clinic Logo" class="logo">
        <h1>
            <hr class="bg-blue-400 h-1">
        </h1>
    </header>

    <!-- Patient Details Section -->
    <!-- <div class="patient-details">
        <p><strong>Name:</strong> <span class="underline" style="width: 15rem;">{{$patient->full_name}}</span> <strong>Date:</strong> <span class="underline">{{ now()->format('F j, Y')}}</span></p>
        <p><strong>Address:</strong> <span class="underline">___________________________</span> <strong>Age:</strong> <span class="underline">______</span> <strong>Sex:</strong> <span class="underline">______</span></p>
    </div> -->
   

    <section class="content hidden">
        <div class="flex flex-col">
            <div class="text-3xl text-center w-full">
                ADMITTING ORDERS
            </div>
            <div class="mt-10 text-[12pt] ">
                To: <u>{{$hospital}} &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;</u>
            </div>
            <div class="mt-10 text-[12pt]">
                - Please admit patient to __________
            </div>
            <div>
                - Secure consent to care
            </div>
            <div>
                - Diet
            </div>
            <div>
                - IVF
            </div>
            <div>
                - Diagnostic
            </div>
            <div class="mt-10">
                - Medications:
            </div>
            <div class="mt-10">
                - VS q4 and I & O qShift
            </div>
            <div>
                - Watchout for unusualities
            </div>
            <div>
                - Kindly inform me once admitted
            </div>
            <div>
                - Refer accordingly
            </div>
            <div class="mt-10">
                - Special instructions(if any):
            </div>
        </div>
    </section>

    <!-- Footer Section -->
    <footer class="footer h-full flex items-end justify-end">
        <div class="physician-signature gap-y-0 text-xs">
            <p>BEN JAY C. PORCADILLA, RMT, MD,FPCP</p>
            <p>License no:0132066</p>
            <p>PTR no: 2173419</p>
            <p>S2 License no: _____________________</p>
        </div>
    </footer>
</div>