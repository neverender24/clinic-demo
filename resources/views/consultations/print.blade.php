
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            color:rgb(31, 31, 32);
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .prescription-container {
            background-color: white;
            width: 39rem;  /* Custom width */
            height: 51rem; /* Custom height */
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            display: flex;
            flex-direction: column; /* Stack content vertically */
            justify-content: space-between; /* Push footer to the bottom */
            overflow: hidden;
        }

        .prescription-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .prescription-header .logo {
            width: 100%; /* Make logo full width of container */
            height: auto; /* Maintain aspect ratio */
            display: block;
        }

        .prescription-header h1 {
            font-size: 2rem;
            margin-top: 10px;
        }

        .patient-details {
            margin-bottom: 20px;
            font-size: 1.1rem;
        }

        .patient-details p {
            margin-bottom: 12px;
        }

        .underline {
            border-bottom: 1px solid black;
            display: inline-block;
            margin-top: 0.2rem;
        }

        .medication-list {
            margin-bottom: 30px;
            margin-top: 40px;
            padding-left: 15px;
        }

        .medication-list h2 {
            font-size: 11pt;
            margin-bottom: 10px;
        }

        .medication-list ul {
            list-style-type: none;
        }

        .medication-list ul li {
            font-size: 11pt;
            margin: 5px 0;
        }

        .medication-list li {
            font-size: 11pt;
            font-weight: normal;
            font-style: normal;
        }

        .prescription-footer {
            /* text-align: right; */
            font-size: 1rem;
            margin-top: auto; /* Ensures it pushes to the bottom if content height is less */
        }

        .physician-signature p {
            margin: 5px 0;
        }

        .parent {
            display: grid;
        }

        .child {
            width: 100%;
        }

        .wrapper {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
        }

        .col-1 {
            grid-column: auto / span 1;
        }

        .col-2 {
            grid-column: auto / span 2;
        }

        .col-3 {
            grid-column: auto / span 3;
        }

        .col-4 {
            grid-column: auto / span 4;
        }

        .col-5 {
            grid-column: auto / span 5;
        }

        
    </style>
<body>
    
    <div class="prescription-container">
        <!-- Heading Section -->
        <header class="prescription-header">
            <img src="{{asset('images/clinic/mati-clinic.png')}}" alt="Clinic Logo" class="logo">
            <h1>
                <hr>
            </h1>
        </header>

        <!-- Patient Details Section -->
        <!-- <div class="patient-details">
            <p><strong>Name:</strong> <span class="underline" style="width: 15rem;">{{$patient->full_name}}</span> <strong>Date:</strong> <span class="underline">{{ now()->format('F j, Y')}}</span></p>
            <p><strong>Address:</strong> <span class="underline">___________________________</span> <strong>Age:</strong> <span class="underline">______</span> <strong>Sex:</strong> <span class="underline">______</span></p>
        </div> -->


        <table style="">
            <tr style="">
                <td colspan="3">
                    <p class="parent">
                        <div class="child">
                            <strong style="display: inline;">Name:</strong> <span style="display: inline;" class="">{{$patient->full_name}}</span>
                        </div>
                    </p> 
                </td>
                <td colspan="2"><strong>Date:</strong> <span class="">{{ now()->format('F j, Y')}}</span></td>
            </tr>
            <tr style="border: solid black 1px;">
                <td colspan="3" style="width: 70%;">
                    <p style="white-space: nowrap;">
                        <strong>Address:</strong> <span class="">{{ $patient->address }}</span>
                    </p>
                </td>
                <td ><strong>Age:</strong> <span class="">{{ Carbon\Carbon::parse($patient->birthday)->age }}</span></td>
                <td style="width: 10%;"><strong>Sex:</strong> <span class="">{{ $patient->sex }}</span></td>
            </tr>
            <!-- <tr>
                <td><strong>Name:</strong> <span class="underline" style="width: 15rem;">{{$patient->full_name}}</span> </td>
                <td><strong>Date:</strong> <span class="underline">{{ now()->format('F j, Y')}}</span></td>
                <td><strong>Date:</strong> <span class="underline">{{ now()->format('F j, Y')}}</span></td>
            </tr> -->
        </table>

        <!-- Body Section -->
        <div class="medication-list">
            <ol class="list-decimal">
                @foreach($medicines as $key => $medicine)
                <li style="margin-top:5px;">
                    <div class="wrapper">
                        <div class="col-5">
                            <span class="inline">{!! $medicine->name !!} {!! $medicine->brand !!} {!! $medicine->pivot->remarks !!}</span>
                            <div>{{$medicine->pivot?->remarks}}</div>
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

        <!-- Footer Section -->
        <footer class="prescription-footer">
            <div class="wrapper">
                <div class="col-3">
                    Next follow-up schedule:
                    <div class="wrapper">
                        <div class="col-4" style="border-bottom: solid black 1px; padding-top: 5px">
                            {{$next_follow_up_schedule}}
                        </div>
                    </div>
                </div>
                <div class="col-3 physician-signature">
                    <p>BEN JAY C. PORCADILLA, RMT, MD,FPCP</p>
                    <p>License no:0132066</p>
                    <p>PTR no: 2173419</p>
                    <p>Signature: _____________________</p>
                </div>
            </div>
        </footer>
        
    </div>
