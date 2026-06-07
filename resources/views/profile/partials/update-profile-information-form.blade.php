<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('diwan.profile.account_info') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('diwan.profile.account_hint') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label :value="__('diwan.profile.username')" />
            <x-text-input class="mt-1 block w-full bg-gray-50 font-mono" type="text" :value="$user->username" disabled />
        </div>

        <div>
            <x-input-label for="name" :value="__('diwan.profile.name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('diwan.profile.email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="email" />
            <p class="mt-1 text-xs text-gray-500">{{ __('diwan.profile.email_hint') }}</p>
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('diwan.profile.save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('diwan.profile.saved') }}</p>
            @endif
        </div>
    </form>
</section>
