<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create New Correspondence
            </h2>
            <a href="{{ route('correspondences.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                Back to Log
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('correspondences.store') }}" enctype="multipart/form-data" class="p-6 space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="category" value="نوع الديوان" />
                            <select id="category" name="category" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">اختر النوع</option>
                                @foreach (\App\Enums\TransactionCategory::cases() as $transactionCategory)
                                    <option value="{{ $transactionCategory->value }}" @selected(old('category') === $transactionCategory->value)>
                                        {{ $transactionCategory->label() }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('category')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="priority" value="Priority" />
                            <select id="priority" name="priority" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="normal" @selected(old('priority', 'normal') === 'normal')>Normal</option>
                                <option value="urgent" @selected(old('priority') === 'urgent')>Urgent</option>
                            </select>
                            <x-input-error :messages="$errors->get('priority')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="sender" value="Sender" />
                            <x-text-input id="sender" name="sender" type="text" class="mt-1 block w-full" :value="old('sender')" required />
                            <x-input-error :messages="$errors->get('sender')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="receiver" value="Receiver" />
                            <x-text-input id="receiver" name="receiver" type="text" class="mt-1 block w-full" :value="old('receiver')" required />
                            <x-input-error :messages="$errors->get('receiver')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="subject" value="Subject" />
                        <x-text-input id="subject" name="subject" type="text" class="mt-1 block w-full" :value="old('subject')" required />
                        <x-input-error :messages="$errors->get('subject')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="content" value="Content" />
                        <textarea id="content" name="content" rows="8" required
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('content') }}</textarea>
                        <x-input-error :messages="$errors->get('content')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="file_path" value="Attachment (PDF / PNG / JPG — max 2MB)" />
                        <input id="file_path" name="file_path" type="file" accept=".pdf,.png,.jpg,.jpeg"
                               class="mt-1 block w-full text-sm text-gray-700 file:me-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <x-input-error :messages="$errors->get('file_path')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('correspondences.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition">
                            Cancel
                        </a>
                        <x-primary-button>
                            Save Correspondence
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
