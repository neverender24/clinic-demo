<x-filament-panels::page>
    <div class="grid grid-cols-3 gap-x-2 mt-3">
        <div class="col-span-2">
            {{ $this->form }}
        </div>
        <div>
            {{ $this->table }}
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
</x-filament-panels::page>
