<x-modal wire:model="modal" title="Archive confirmation"
    subtitle="You are archiving the opportunity {{ $opportunity?->title }}">

    <x-slot:actions>
        <x-button label="Hum... no" @click="$wire.modal = false" />
        <x-button label="Yes, I am" class="btn-primary" wire:click='archive' />
    </x-slot:actions>
</x-modal>
