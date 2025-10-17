

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
    u {
        text-decoration: none; /* remove default underline */
        border-bottom: 2px solid #000;
        line-height: 1; /* reduce gap */
        display: inline-block; /* avoid descender space issues */
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
            
            {{-- <div class="absolute inset-0 bg-no-repeat bg-center opacity-15" style="background-image: url('{{$watermark}}');"></div> --}}
            
            <!-- Your child content, unaffected by parent opacity -->
            <div class="relative">
                <div class="text-content px-5 py-5 flex flex-col">
                    <div class="flex justify-between">
                        <div class=" flex p-0">
                            <strong style="display: inline;">Name:</strong> <span style="display: inline;" class="ms-1"> {{$patient->full_name}}</span>
                        </div>
                        <div class="">
                            <strong>Date:</strong> <span class="w-full border-b">{{ now()->format('F j, Y')}}</span>
                        </div>
                    </div>
                    <div class="flex justify-between">
                        <div class="col-span-3">
                            <strong style="display: inline;">Address:</strong> <span style="display: inline;" class=""> {{$patient->address}}</span>
                        </div>
                        <div class="basis-1/4 flex justify-between
                        ">
                            <div class="col-span-1">
                                <strong>Age:</strong> <span class="">{{ Carbon\Carbon::parse($patient->birthday)->age}}</span>
                            </div>
                            <div class="col-span-1">
                                <strong>Sex:</strong> <span class="">{{ $patient->sex }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-content px-5 py-5 space-y-6">
                    <p class="">
                        {{-- <span class="">To whom it may concern,</span> --}}
                    </p>
                    <p class="text-justify">This certifies that the above-mentioned patient
                        was seen and evaluated at this clinic/hospital on <u>{{$consultation_date}}</u> due to <u>{!!$chief_complaint!!}</u>
                    </p>
                    {{-- <div class="flex">
                        <div class=""> Diagnosis: </div>  <span class="border-b border-b border-gray-950">testsdfdsf</span>
                        
                    </div> --}}
                    <div class="diagnosis">
                        Diagnosis:
                        <br>
                        {!!$diagnosis!!}
                    </div>
                    <p>
                        This patient is advised to have
                        {{-- @if ()
                            
                        @endif  --}}
                        <u>{{($approximate_days == 'N/A' ? '' : $approximate_days_in_word)}} {{$approximate_days}}</u> days of rest, from <u>{{(!$estimated_date ? 'N/A' : $estimated_date)}}</u>
                         to <u>{{(!$estimated_date_to ? 'N/A' : $estimated_date_to)}}</u>
                        to allow for complete recovery.
                    </p>
                    <p>
                        The patient is fit to return to work/school on <u>{{!$return_date ? 'N/A' : $return_date}}</u>
                    </p>
                    <div class="flex flex-col gap-y-0.5">
                        <div>Remarks: </div>
                        <div>
                            <u>{!!$medical_cert_remarks!!}</u>
                        </div>
                    </div>
                    <p class="italic">
                        This certification is being issued upon the request of the above-mentioned individual for whatever purpose it may serve except for medico-legal purposes.
                    </p>
                </div>
                <!-- More child elements -->
            </div>
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
        <!-- <div class="relative bg-no-repeat bg-cover opacity-15 min-h-screen" style="background-image: url('');">
            <div class="absolute inset-0">
                <div class="relative z-10 opacity-100">
                <h1 class="text-gray-800">Content Here</h1>
                </div>
            </div>
        </div> -->
    </div>
</div>