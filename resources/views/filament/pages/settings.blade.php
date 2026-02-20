<x-filament-panels::page>
    <div x-data x-on:keydown.window.prevent.ctrl.s="$wire.save()">
        {{ $this->form }}
    </div>
</x-filament-panels::page>
