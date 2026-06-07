<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('diwan.verify.title') }} — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="fixed top-0 inset-x-0 z-50 flex justify-end px-4 py-3 pointer-events-none">
        <div class="pointer-events-auto">
            <x-locale-switcher />
        </div>
    </div>

    <div class="min-h-screen py-16 px-4">
        <div class="max-w-3xl mx-auto space-y-6">
            <div class="text-center">
                <h1 class="text-2xl font-bold text-gray-900">{{ __('diwan.verify.title') }}</h1>
                <p class="text-sm text-gray-500 mt-2">{{ __('diwan.verify_subtitle') }}</p>
            </div>

            <div class="bg-white shadow-sm rounded-lg p-6 space-y-6">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">{{ __('diwan.verify.user') }}</dt>
                        <dd class="font-semibold text-gray-900">{{ $document->user->username }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('diwan.verify.transaction_type') }}</dt>
                        <dd class="text-gray-900">{{ $document->type->label() }}</dd>
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
                        <img src="{{ route('documents.verify.stream', [
                            'username' => $document->user->username,
                            'doctype' => $document->type->value,
                            'date' => $document->upload_date,
                            'sequence' => $document->formattedSequence(),
                        ]) }}" alt="{{ $document->original_filename }}" class="max-w-full rounded-lg border border-gray-200">
                    @else
                        <iframe
                            src="{{ route('documents.verify.stream', [
                                'username' => $document->user->username,
                                'doctype' => $document->type->value,
                                'date' => $document->upload_date,
                                'sequence' => $document->formattedSequence(),
                            ]) }}"
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
