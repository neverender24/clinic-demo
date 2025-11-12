{{-- <x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        x-data="{ state: $wire.$entangle(@js($getStatePath())) }"
        {{ $getExtraAttributeBag() }}
    >
        
    </div>
</x-dynamic-component> --}}

@livewire('list-of-consultation', ['patient_id' => $patient_id])