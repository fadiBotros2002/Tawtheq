<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('diwan.documents.create_title') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            @if ($categories->isEmpty())
                <div class="bg-amber-50 border border-amber-200 text-amber-900 px-4 py-3 rounded-lg mb-6">
                    {{ __('diwan.documents.no_categories_hint') }}
                    <a href="{{ route('categories.create') }}" class="font-medium text-indigo-600 hover:text-indigo-900 ms-1">
                        {{ __('diwan.categories.create_first') }}
                    </a>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data" class="space-y-6" @if ($categories->isEmpty()) aria-disabled="true" @endif>
                    @csrf

                    <div>
                        <x-input-label for="name" :value="__('diwan.documents.name')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required />
                        <p class="mt-1 text-xs text-gray-500">{{ __('diwan.documents.name_hint') }}</p>
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="category_id" :value="__('diwan.documents.category')" />
                        <select name="category_id" id="category_id" required @disabled($categories->isEmpty())
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100 disabled:cursor-not-allowed">
                            <option value="">{{ __('diwan.documents.choose_category') }}</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected((string) old('category_id') === (string) $category->id)>
                                    {{ $category->label() }} ({{ $category->slug }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                        <p class="mt-1 text-xs text-gray-500">
                            <a href="{{ route('categories.create') }}" class="text-indigo-600 hover:text-indigo-900">{{ __('diwan.categories.add_new') }}</a>
                        </p>
                    </div>

                    <div>
                        <x-input-label for="type" :value="__('diwan.documents.transaction_type')" />
                        <select name="type" id="type" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('diwan.documents.choose_type') }}</option>
                            @foreach ($types as $type)
                                <option value="{{ $type->value }}" @selected(old('type') === $type->value)>
                                    {{ $type->label() }} ({{ $type->value }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('type')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="file" :value="__('diwan.documents.file_input')" />
                        <input type="file" name="file" id="file" required accept=".pdf,.jpg,.jpeg,.png"
                               class="mt-1 block w-full text-sm text-gray-700 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <x-input-error :messages="$errors->get('file')" class="mt-2" />
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button :disabled="$categories->isEmpty()">{{ __('diwan.documents.submit_upload') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
