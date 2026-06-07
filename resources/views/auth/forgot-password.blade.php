<x-guest-layout>
    <h1 class="text-xl font-bold text-gray-900 mb-4 text-center">{{ __('diwan.auth.forgot_title') }}</h1>

    <div class="mb-4 text-sm text-gray-600">
        {{ __('diwan.auth.forgot_hint') }}
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('diwan.profile.email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4">
            <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900">
                {{ __('diwan.auth.back_to_login') }}
            </a>

            <x-primary-button>
                {{ __('diwan.auth.send_reset_link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
