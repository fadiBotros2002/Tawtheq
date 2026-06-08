<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('diwan.categories.create_title') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('categories.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="name_ar" :value="__('diwan.categories.name_ar')" />
                        <x-text-input id="name_ar" name="name_ar" type="text" class="mt-1 block w-full"
                                      :value="old('name_ar')" required autofocus />
                        <x-input-error :messages="$errors->get('name_ar')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="name_en" :value="__('diwan.categories.name_en')" />
                        <x-text-input id="name_en" name="name_en" type="text" class="mt-1 block w-full"
                                      :value="old('name_en')" required />
                        <x-input-error :messages="$errors->get('name_en')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="slug" :value="__('diwan.categories.slug')" />
                        <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full font-mono"
                                      :value="old('slug')" required />
                        <p class="mt-1 text-xs text-gray-500">{{ __('diwan.categories.slug_hint') }}</p>
                        <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('categories.index') }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('diwan.admin.cancel') }}</a>
                        <x-primary-button>{{ __('diwan.categories.submit') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
