<x-card title="Login" class="mx-auto w-[450px]">

    @if ($errors->hasAny(['invalidCredentials', 'rateLimiter']))
        <x-alert icon="o-home" class="alert-warning mb-4">
            @error('invalidCredentials')
                <span>{{ $message }}</span>
            @enderror

            @error('rateLimiter')
                <span>{{ $message }}</span>
            @enderror
        </x-alert>
    @endif


    <x-form wire:submit="tryToLogin">
        <x-input label="E-Mail" wire:model="email" />
        <x-input label="Password" wire:model="password" type="password" />
        <x-slot:actions>
            <div class="w-full flex items-center justify-between">
                <a wire:navegate href="{{ route('auth.register') }}" class="link-primary">
                    I want to create an account
                </a>
                <div>
                    <x-button label="Login" class="btn-primary " type="submit" spinner="submit" />
                </div>
            </div>

        </x-slot:actions>
    </x-form>
</x-card>
