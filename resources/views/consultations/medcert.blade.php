

<style >
    @page {
        size: letter;
        margin: 0;
    }
    .paper-size {
        width: 8.5in;
        height: 11in;
    }
    @media print {
        @page {
            size: letter;
            margin: 0;
        }
        body {
            font-family: Arial, sans-serif;
            padding: 1in;
            width: 8.5in;
            height: 11in;
        }
    }
</style>
<div class="flex justify-center paper-size" style="font-size: 11pt;">
    <div class="bg-white h-full pl-14 pr-10" style="width: 8.5in;">
        <div class="bg-transparent">
            <header class="prescription-header">
                <img src="{{$header_image}}" alt="Clinic Logo" class="object-cover w-full">
            </header>
            <h2 class="text-4xl font-bold text-content text-center border-t border-gray-400 pt-5">Medical Certificate</h2>
        </div>
        <div class="relative min-h-screen">
            <!-- Absolute background image -->
            <div class="absolute inset-0 bg-no-repeat bg-center opacity-15" style="background-image: url('{{$watermark}}');"></div>
            
            <!-- Your child content, unaffected by parent opacity -->
            <div class="relative">
                <div class="text-content px-5 py-5">
                    <p class="text-end">
                        <span class="font-bold">Date:</span>
                        <span class="underline">{{now()->format('F j, Y')}}</span>
                    </p>
                </div>
                <div class="text-content px-5 py-5 space-y-6">
                    <p class="">
                        <span class="">To whom it may concern,</span>
                    </p>
                    <p>This certifies that 
                        <span class="underline font-bold">{{$patient->full_name}}</span> 
                        sought medical consultation on 
                        <span class="underline font-bold">{{$consultation_date}}.</span>
                        During the consultation, the patient presented with clinical sign and symptoms suggestive 
                        of the following medical condition/s: 
                        <div class="border-b border-gray-900"> {!! $diagnosis !!} </div>
                        <div class="border-b border-gray-900"> </div>
                    </p>
                    <p>
                        The anticipated duration of the patient's recovery is estimated to be approximately <span class="underline font-bold">{{$approximate_days}} day/s</span>, 
                        after which the patient is expected to be fit for resuming regular activies, including work, 
 font-bold                        on or around <span class="underline font-bold">{{$estimated_date}}</span>.
                    </p>
                    <p>
                        Please be advised that this certificate is issued at the patient's request and for their specific
                        purposes. However, it is important to note that this document is not legally valid in a court of law.
                    </p>
                    <p>
                        <span class="font-bold">Remarks:</span>  
                        <div class="border-b border-gray-900"> {!!$medical_cert_remarks!!} </div>
                        <div class="border-b border-gray-900">  </div>
                    </p>
                    <p class="">
                        <br>
                        <br>
                        <div class="col-span-3 physician-signature gap-y-0  flex justify-end">
                            <div>
                                <span class="font-bold ">Attending Physician:</span>
                                <p class="mt-10 font-bold">BEN JAY C. PORCADILLA, RMT, MD, FPCP</p>
                                <p>License no: 0132066</p>
                                <p>PTR no: 2173419</p>
                                <p>S2 License no: _____________________</p>
                            </div>
                        </div>

                    </p>
                </div>
                <!-- More child elements -->
            </div>
        </div>
        <!-- <div class="relative bg-no-repeat bg-cover opacity-15 min-h-screen" style="background-image: url('');">
            <div class="absolute inset-0">
                <div class="relative z-10 opacity-100">
                <h1 class="text-gray-800">Content Here</h1>
                </div>
            </div>
        </div> -->
    </div>
</div>