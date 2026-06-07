<x-guest-layout>
    <h1 class="text-xl font-bold text-gray-900 mb-6 text-center">{{ __('diwan.auth.login_title') }}</h1>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="username" :value="__('diwan.auth.username')" />
            <x-text-input id="username" class="block mt-1 w-full font-mono" type="text" name="username" :value="old('username')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('diwan.auth.password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('diwan.auth.remember_me') }}</span>
            </label>

            <a href="{{ route('password.request') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                {{ __('diwan.auth.forgot_password') }}
            </a>
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="ms-3">
                {{ __('diwan.auth.login') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
