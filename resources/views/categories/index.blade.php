<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('diwan.categories.index_title') }}</h2>
            <a href="{{ route('categories.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-xs font-semibold uppercase rounded-md hover:bg-indigo-700">
                {{ __('diwan.categories.new') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">{{ __('diwan.categories.name_ar') }}</th>
                            <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">{{ __('diwan.categories.name_en') }}</th>
                            <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">{{ __('diwan.categories.slug') }}</th>
                            <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">{{ __('diwan.categories.documents_count') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($categories as $category)
                            <tr>
                                <td class="px-6 py-4 text-sm">{{ $category->name_ar }}</td>
                                <td class="px-6 py-4 text-sm">{{ $category->name_en }}</td>
                                <td class="px-6 py-4 text-sm font-mono text-gray-700">{{ $category->slug }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $category->documents_count }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                    {{ __('diwan.categories.empty') }}
                                    <a href="{{ route('categories.create') }}" class="text-indigo-600 hover:text-indigo-900 font-medium ms-1">
                                        {{ __('diwan.categories.create_first') }}
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if ($categories->hasPages())
                    <div class="px-6 py-4 border-t">{{ $categories->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
