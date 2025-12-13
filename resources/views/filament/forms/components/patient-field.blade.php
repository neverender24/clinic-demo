<div>
    @if ($field->getName() == 'patient_detail')

        @livewire('patient-detail', ['patient_id' => $get('patient_id')])

    @elseif($field->getName() == 'patient_history')

        @livewire('list-of-consultation', ['patient_id' => $get('patient_id'), 'date' => $get('date')])

    @elseif($field->getName() == 'hospital_admission')

        @livewire('list-hospital-admission', ['patient_id' => $get('patient_id')])

    @endif
</div>