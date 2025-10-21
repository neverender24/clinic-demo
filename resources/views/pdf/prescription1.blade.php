@extends('pdf.app')
@section('links')
@vite('resources/css/pdf/prescription.css')
@endsection

@section('main')
 @foreach($medicines as $row)
    <div class="prescription-container">
        <div class="page" style="display: flex; flex-direction: column; height: 100%;">
            @include('pdf.header',['title' => 'PRESCRIPTION'])
    
            <div class="content" style="flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div class="patient-info">
                        <div class="patient-flex">
                            <div><strong>Name:</strong> {{ $patient->full_name }}</div>
                            <div><strong>Date:</strong> {{ now()->format('F j, Y') }}</div>
                        </div>
                        <div class="patient-flex">
                            <div><strong>Address:</strong> {{ $patient->address }}</div>
                            <div>
                                <strong>Age:</strong> {{ \Carbon\Carbon::parse($patient->birthday)->age }} &nbsp;
                                <strong>Sex:</strong> {{ $patient->sex }}
                            </div>
                        </div>
                    </div>
    
                    <div class="medicine-info" style="break-inside: avoid;">
                        <img src="{{ asset('images/clinic/rx.png') }}" alt="Rx">
                        <ol class="medicine-list">
                            @foreach($row as $index => $medicine)
                            <li class="medicine-item">
                                <div class="medicine-header">
                                    <span>{{ $medicine->name }}</span>
                                    <span>#{{ $medicine->pivot?->quantity }}</span>
                                </div>
                                @if($medicine->brand)
                                <div class="medicine-brand">({{ $medicine->brand }})</div>
                                @endif
                                <div class="medicine-sig">Sig. {{ $medicine->pivot?->remarks }}</div>
                            </li>
                            @endforeach
                        </ol>
                    </div>
                </div>


                <div class="content">
                    <p class="followup-signature">
                        <span class="followup">Next follow-up schedule: ___________________</span>
                        <span class="signature">
                            <strong>ANTONIO P. YBIERNAS JR., M.D., COHC</strong><br>
                            Otolaryngology - Head and Neck Surgery Specialist<br>
                            Lic Number: 0085800<br>
                            PTR Number: ___________
                        </span>
                    </p>
                </div>

                {{-- Footer text (centered below signature) --}}
                <div class="page-footer">
                    @include('pdf.footer')
                </div>
    
                {{-- <footer class="footer" style="margin-top: 20px;">
                    <div>
                        <div>Next follow-up schedule:</div>
                        <div class="underline">{{ $next_follow_up_schedule }}</div>
                    </div>
                    <div class="signature">
                        <p><strong>ANTONIO P. YBIERNAS JR., M.D., COHC</strong></p>
                        <p>Otolaryngology - Head and Neck Surgery Specialist</p>
                        <p>Lic Number: 0085800</p>
                        <p>PTR Number: {{$ptr}}</p>
                    </div>
                </footer> --}}
            </div>
        </div>
    </div>
    @endforeach

@endsection