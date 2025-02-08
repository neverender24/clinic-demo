<style>
   .prescription-container {
        /* background-color: white;
        width: 556.8px;  
        */
        width: 556.8px;  
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

@foreach($medicines as $row)
<div class="prescription-container bg-white shadow pl-14 pr-10 flex flex-col space-y-5 py-5 pb-10 text-[9pt]" id="prescription">
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

        <section class="content">
            <div class="grid grid-cols-5">
                <div class="col-span-3 flex p-0">
                    <div class="font-bold">Name:</div> <div class="w-96 py-0 my-0 uppercase">{{$patient->full_name}}</div>
                </div>
                <div class="col-span-2">
                    <strong>Date:</strong> <span class="w-full border-b-1">{{ now()->format('F j, Y')}}</span>
                </div>
                <div class="col-span-3">
                    <strong style="display: inline;">Address:</strong> <span style="display: inline;" class="">{{$patient->address}}</span>
                </div>
                <div class="col-span-1">
                    <strong>Age:</strong> <span class="">{{ Carbon\Carbon::parse($patient->birthday)->age}}</span>
                </div>
                <div class="col-span-1">
                    <strong>Sex:</strong> <span class="">{{ $patient->sex }}</span>
                </div>
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
                                <div class="font-bold">({!! $medicine->brand !!})</div>
                                <div>Sig {{$medicine->pivot?->remarks}}</div>
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

        <!-- Footer Section -->
        <footer class="footer h-full flex items-end ">
            <div class="grid grid-cols-6 gap-x-5 mt-auto w-full">
                <div class="col-span-3 flex flex-col justify-between pb-2">
                    <div>
                    Next follow-up schedule:
                    </div>
                    <div class="border-b border-gray-700 mt-auto">
                       {{$next_follow_up_schedule}}
                    </div> 
                </div>
                <div class="col-span-3 physician-signature gap-y-0 text-[7pt] flex justify-end">
                   <div>
                     <p>BEN JAY C. PORCADILLA, RMT, MD,FPCP</p>
                     <p>License no:0132066</p>
                     <p>PTR no: 2173419</p>
                     <p>S2 License no: _____________________</p>
                   </div>
                </div>
            </div>
        </footer>
    </div>
@endforeach