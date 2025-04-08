<x-filament::page>
    {{ $this->form }}

    <div class="mt-4">
        <x-filament::button wire:click="save">
            Simpan Perubahan
        </x-filament::button>
    </div>
</x-filament::page>
