<x-guest-layout>
    <h1 class="text-xl font-bold text-gray-900 mb-4 text-center">Forgot Password</h1>

    <div class="mb-4 text-sm text-gray-600">
        Forgot your password? No problem. Enter your email address and we will send you a password reset link.
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                Email Password Reset Link
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
