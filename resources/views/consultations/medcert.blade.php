
<div class="flex justify-center  min-h-screen" style="font-size: 12pt;">
    <div class="bg-white h-full pl-14 pr-10" style="width: 8.5in;">
        <div class="bg-transparent">
            <header class="prescription-header">
                <img src="{{$header_image}}" alt="Clinic Logo" class="object-cover w-full">
            </header>
            <h2 class="text-4xl font-bold text-content text-center border-t border-gray-400 pt-5">Medical Certificate</h2>
        </div>
        <div class="relative min-h-screen">
            <!-- Absolute background image -->
            <div class="absolute inset-0 bg-no-repeat bg-center bg-auto bg-contain opacity-15 " style="background-image: url('{{$watermark}}');"></div>
            
            <!-- Your child content, unaffected by parent opacity -->
            <div class="relative">
                <div class="text-content px-5 py-5">
                    <p class="text-end">
                        <span class="font-bold">Date:</span>
                        <span class="underline">{{now()->format('F j, Y')}}</span>
                    </p>
                </div>
                <div class="text-content px-5 py-5 space-y-7">
                    <p class="">
                        <span class="">To whom it may concern,</span>
                    </p>
                    <p>This certifies that 
                        <span class="underline font-bold">{{$patient->full_name}}</span> 
                        sought medical consultation on 
                        <span class="underline font-bold">{{$consultation_date}}.</span>
                        During the consultation, the patient presented with clinical sign and symptoms suggestive 
                        of the following medical condition/s: 
                        <div class="border-b border-gray-900"> </div>
                        <div class="border-b border-gray-900"> </div>
                        <div class="border-b border-gray-900"> </div>
                    </p>
                    <p>
                        The anticipated duration of the patient's recovery is estimated to be approximately ____ day/s, 
                        after which the patient is expected to be fit for resuming regular activies, including work, 
                        on or around _______________.
                    </p>
                    <p>
                        Please be advised that this certificate is issued at the patient's request and for their specific
                        purposes. However, it is important to note that this document is not legally valid i a cout of law.
                    </p>
                    <p>
                        <span class="font-bold">Remarks:</span>  
                        <div class="border-b border-gray-900"> </div>
                        <div class="border-b border-gray-900"> </div>
                        <div class="border-b border-gray-900"> </div>
                    </p>
                    <p class="pt-10">
                         <span class="font-bold">Attending Physician:</span>
                         
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