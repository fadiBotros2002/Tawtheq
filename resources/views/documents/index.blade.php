<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('diwan.documents.index_title') }}
            </h2>
            <a href="{{ route('documents.create') }}"
               class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                {{ __('diwan.documents.upload_btn') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">{{ __('diwan.documents.sequence') }}</th>
                                <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">{{ __('diwan.documents.type') }}</th>
                                <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">{{ __('diwan.documents.user') }}</th>
                                <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">{{ __('diwan.documents.date') }}</th>
                                <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">{{ __('diwan.documents.file') }}</th>
                                <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">{{ __('diwan.documents.action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($documents as $document)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">
                                        {{ $document->formattedSequence() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $document->type->value === 'inbound' ? 'bg-blue-100 text-blue-800' : 'bg-emerald-100 text-emerald-800' }}">
                                            {{ $document->type->label() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $document->user->username }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $document->upload_date }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700 max-w-xs truncate">{{ $document->original_filename }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <a href="{{ route('documents.show', $document) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                            {{ __('diwan.documents.view') }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        {{ __('diwan.documents.empty') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($documents->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $documents->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
