<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('diwan.admin.create_title') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="username" :value="__('diwan.admin.username')" />
                        <x-text-input id="username" name="username" type="text" class="mt-1 block w-full font-mono"
                                      :value="old('username')" required autofocus />
                        <p class="mt-1 text-xs text-gray-500">{{ __('diwan.admin.username_hint') }}</p>
                        <x-input-error :messages="$errors->get('username')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="name" :value="__('diwan.admin.full_name')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                      :value="old('name')" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="email" :value="__('diwan.admin.email')" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                      :value="old('email')" required autocomplete="email" />
                        <p class="mt-1 text-xs text-gray-500">{{ __('diwan.admin.email_hint') }}</p>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>



                    <div>
                        <x-input-label for="password" :value="__('diwan.admin.password')" />
                        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" :value="__('diwan.admin.confirm_password')" />
                        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('diwan.admin.cancel') }}</a>
                        <x-primary-button>{{ __('diwan.admin.create') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
