@php
    $tenant = \Filament\Facades\Filament::getTenant();
@endphp

<x-filament-panels::page>
    <x-filament-panels::form wire:submit="cetak">
        {{ $this->form }}

        <div class="flex justify-end mt-4">
            <x-filament::button type="submit" icon="heroicon-m-printer" size="lg">
                Cetak PDF
            </x-filament::button>
        </div>
    </x-filament-panels::form>
</x-filament-panels::page>