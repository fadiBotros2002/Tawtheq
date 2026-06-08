<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('diwan.verify.title') }} — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen py-8 sm:py-12 px-4">
        <div class="max-w-3xl mx-auto space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div class="text-center sm:text-start flex-1">
                    <h1 class="text-2xl font-bold text-gray-900">{{ __('diwan.verify.title') }}</h1>
                    <p class="text-sm text-gray-500 mt-2">{{ __('diwan.verify_subtitle') }}</p>
                </div>
                <div class="flex justify-center sm:justify-end shrink-0">
                    <x-locale-switcher
                        labeled
                        :align="app()->getLocale() === 'ar' ? 'left' : 'right'"
                    />
                </div>
            </div>

            <div class="flex items-center gap-4 rounded-xl border border-green-300 bg-green-100 px-5 py-4 shadow-sm">
                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-green-600 text-white shadow-md ring-4 ring-green-200">
                    <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <p class="text-lg font-semibold text-green-900">{{ __('diwan.verify.verified') }}</p>
                    <p class="text-sm text-green-800 mt-0.5">{{ __('diwan.verify.verified_hint') }}</p>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg p-6 space-y-6">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div class="sm:col-span-2">
                        <dt class="text-gray-500">{{ __('diwan.verify.document_name') }}</dt>
                        <dd class="font-semibold text-gray-900">{{ $document->name }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-gray-500">{{ __('diwan.verify.reference_number') }}</dt>
                        <dd class="font-mono text-sm text-gray-900">{{ $document->reference_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('diwan.verify.user') }}</dt>
                        <dd class="font-semibold text-gray-900">{{ $document->user->username }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('diwan.verify.transaction_type') }}</dt>
                        <dd class="text-gray-900">{{ $document->type->label() }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('diwan.verify.category') }}</dt>
                        <dd class="text-gray-900">{{ $document->category->label() }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('diwan.verify.upload_date') }}</dt>
                        <dd class="text-gray-900">{{ $document->upload_date }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('diwan.verify.sequence') }}</dt>
                        <dd class="font-mono font-semibold text-gray-900">{{ $document->formattedSequence() }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-gray-500">{{ __('diwan.verify.filename') }}</dt>
                        <dd class="text-gray-900">{{ $document->original_filename }}</dd>
                    </div>
                </dl>

                <div class="border-t border-gray-100 pt-6">
                    <p class="text-sm text-gray-500 mb-3">{{ __('diwan.verify.preview') }}</p>
                    @if (str_starts_with($document->mime_type ?? '', 'image/'))
                        <img src="{{ route('documents.verify.stream', $document->verifyRouteParams()) }}" alt="{{ $document->original_filename }}" class="max-w-full rounded-lg border border-gray-200">
                    @else
                        <iframe
                            src="{{ route('documents.verify.stream', $document->verifyRouteParams()) }}"
                            class="w-full h-[70vh] rounded-lg border border-gray-200"
                            title="{{ $document->original_filename }}">
                        </iframe>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>
