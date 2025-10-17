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
        }
        /* .prescription-container {
            width: 556.8px;
            height: 797px;
            page-break-after: always;
            margin: 1in;
        } */
    }
    
    
</style>

@foreach($medicines as $row)
<div class="prescription-container bg-white shadow-sm flex flex-col text-[9pt] p-[10mm]" id="prescription">
        <!-- Heading Section -->
        <header class="header">
            <img src="{{asset('storage/'.$header_image)}}" alt="Clinic Logo" class="logo">
            <h1>
                <hr>
            </h1>
        </header>

        <!-- Patient Details Section -->
        <!-- <div class="patient-details">
            <p><strong>Name:</strong> <span class="underline" style="width: 15rem;">{{$patient->full_name}}</span> <strong>Date:</strong> <span class="underline">{{ now()->format('F j, Y')}}</span></p>
            <p><strong>Address:</strong> <span class="underline">___________________________</span> <strong>Age:</strong> <span class="underline">______</span> <strong>Sex:</strong> <span class="underline">______</span></p>
        </div> -->

        <section class="content ">
            <div class="grid grid-cols-5 mt-2">
                <div class="col-span-3 flex p-0">
                    <strong style="display: inline;">Name:</strong> <span style="display: inline;" class="ms-1"> {{$patient->full_name}}</span>
                </div>
                <div class="col-span-2">
                    <strong>Date:</strong> <span class="w-full border-b">{{ now()->format('F j, Y')}}</span>
                </div>
                <div class="col-span-3">
                    <strong style="display: inline;">Address:</strong> <span style="display: inline;" class=""> {{$patient->address}}</span>
                </div>
                <div class="col-span-1">
                    <strong>Age:</strong> <span class="">{{ Carbon\Carbon::parse($patient->birthday)->age}}</span>
                </div>
                <div class="col-span-1">
                    <strong>Sex:</strong> <span class="">{{ $patient->sex }}</span>
                </div>
            </div>
            <div>
                <img src="{{asset('images/clinic/rx.png')}}" alt="Rx" class="w-20">
            </div>
            
            <!-- <table style="">
                <tr style="">
                    <td colspan="3">
                        <p class="parent">
                            <div class="child">
                                <strong style="display: inline;">Name:</strong> <span style="display: inline;" class="">{{$patient->full_name}}</span>
                            </div>
                        </p> 
                    </td>
                    <td><strong>Date:</strong> <span class="">{{ now()->format('F j, Y')}}</span></td>
                </tr>
                <tr>
                    <td colspan="3">
                    <strong>Address:</strong>
                    <p >
                            <span class="">{{ $patient->address }}</span>
                    </p>
                    </td>
                    <td><strong>Age:</strong> <span class="">{{ Carbon\Carbon::parse($patient->birthday)->age }}</span></td>
                    <td ><strong>Sex:</strong> <span class="">{{ $patient->sex }}</span></td>
                    
                </tr>
            </table> -->
            
            <!-- Body Section -->
            <div class="medication-list">
                <ol class="list-decimal list-outside ps-5">
                    @foreach($row as $key => $medicine)
                    <li class="text-[8pt]">
                        <div class="grid grid-cols-6">
                            <div class="col-span-5 gap-y-0 leading">
                                <span class="inline">{!! $medicine->name !!}</span>
                                <div class="font-bold {{ $medicine->brand ? '' : 'hidden'}}">({!! $medicine->brand !!})</div>
                                <div>Sig. {{$medicine->pivot?->remarks}}</div>
                            </div>
                            <div>
                                <strong class="inline">#{{ $medicine->pivot?->quantity }}</strong>
                            </div>
                        </div>
                        
                    </li>
                    @endforeach
                </ol>
                <!-- <ul>
                    <li>Amoxicillin 500mg - Take 1 capsule 3 times a day for 7 days</li>
                    <li>Ibuprofen 200mg - Take 1 tablet every 6 hours as needed</li>
                    <li>Loratadine 10mg - Take 1 tablet daily</li>
                </ul> -->
            </div>
        </section>
        <footer class="footer h-full flex items-end">
            <div class="grid grid-cols-6 gap-x-5 mt-auto w-full">
                <div class="col-span-3 flex flex-col justify-between pb-2">
                    <div>
                    Next follow-up schedule:
                    </div>
                    <div class=" mt-auto">
                        <div class="border-b border-gray-700 w-1/2">
                            {{$next_follow_up_schedule}}
                        </div>
                    </div> 
                </div>
                <div class="col-span-3 physician-signature gap-y-0 text-[7pt] flex justify-end">
                   <div>
                     <p class="font-bold">BEN JAY C. PORCADILLA, RMT, MD, FPCP</p>
                     <p>License no: 0132066</p>
                     <p>PTR no: 2173419</p>
                     <p>S2 License no: _____________________</p>
                   </div>
                </div>
            </div>
        </footer>

        <!-- Footer Section -->
    </div>
@endforeach