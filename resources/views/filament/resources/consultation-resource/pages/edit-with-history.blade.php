<x-filament-panels::page>
    <div class="grid grid-cols-3 gap-x-2 mt-3">
        <div class="col-span-2">
            {{ $this->form }}
        </div>
        <div class="flex flex-col gap-y-3">
            <div>
                {{ $this->table }}
            </div>
            <div>
                @livewire('list-hospital-admission', ['patient_id' => $record->patient_id])
            </div>
        </div>
        <div>
            <x-filament::button wire:click="submit" class="mt-3">
                Save Changes
            </x-filament::button>
            <x-filament::button color="gray" class="mt-3" href="{{ url()->previous() }}" tag="a">
                Cancel
            </x-filament::button>
        </div>
    </div>

    <x-filament::modal id="history-modal" width="7xl">
        <x-slot name="heading">
            Consultation Details {{Carbon\Carbon::parse($this->historyData->date)->format('F j, Y')}}
        </x-slot>
        
    </x-filament::modal>
</x-filament-panels::page>
