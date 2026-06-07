<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('diwan.documents.show_title') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-6"
                 x-data="{
                     copied: false,
                     url: @js($document->verifyUrl()),
                     qrFilename: @js('qr-' . $document->formattedSequence() . '.png'),
                     copyUrl() {
                         navigator.clipboard.writeText(this.url).then(() => {
                             this.copied = true;
                             setTimeout(() => this.copied = false, 2000);
                         });
                     },
                     downloadQr() {
                         const svg = this.$refs.qrContainer?.querySelector('svg');
                         if (! svg) return;

                         const svgData = new XMLSerializer().serializeToString(svg);
                         const blob = new Blob([svgData], { type: 'image/svg+xml;charset=utf-8' });
                         const blobUrl = URL.createObjectURL(blob);
                         const img = new Image();

                         img.onload = () => {
                             const size = 400;
                             const canvas = document.createElement('canvas');
                             canvas.width = size;
                             canvas.height = size;
                             const ctx = canvas.getContext('2d');
                             ctx.fillStyle = '#ffffff';
                             ctx.fillRect(0, 0, size, size);
                             ctx.drawImage(img, 0, 0, size, size);
                             canvas.toBlob((pngBlob) => {
                                 const pngUrl = URL.createObjectURL(pngBlob);
                                 const link = document.createElement('a');
                                 link.href = pngUrl;
                                 link.download = this.qrFilename;
                                 link.click();
                                 URL.revokeObjectURL(pngUrl);
                                 URL.revokeObjectURL(blobUrl);
                             }, 'image/png');
                         };

                         img.src = blobUrl;
                     },
                 }">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">{{ __('diwan.documents.sequence') }}</dt>
                        <dd class="font-mono font-semibold text-gray-900">{{ $document->formattedSequence() }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('diwan.documents.type') }}</dt>
                        <dd class="text-gray-900">{{ $document->type->label() }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('diwan.documents.upload_date') }}</dt>
                        <dd class="text-gray-900">{{ $document->upload_date }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('diwan.documents.file') }}</dt>
                        <dd class="text-gray-900">{{ $document->original_filename }}</dd>
                    </div>
                </dl>

                <div>
                    <p class="text-sm text-gray-500 mb-2">{{ __('diwan.documents.verify_url') }}</p>
                    <div class="flex flex-wrap items-center gap-2">
                        <input type="text" readonly :value="url"
                               class="flex-1 min-w-0 rounded-md border-gray-300 bg-gray-50 text-sm font-mono">
                        <button type="button" @click="copyUrl()"
                                class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 text-sm font-medium rounded-md hover:bg-gray-50 whitespace-nowrap">
                            <span x-text="copied ? @js(__('diwan.documents.copied')) : @js(__('diwan.documents.copy_link'))"></span>
                        </button>
                        <a href="{{ $document->verifyUrl() }}" target="_blank"
                           class="inline-flex items-center px-3 py-2 text-indigo-600 hover:text-indigo-900 text-sm font-medium whitespace-nowrap">
                            {{ __('diwan.documents.open') }}
                        </a>
                    </div>
                </div>

                <div class="flex flex-col items-center gap-4 pt-4 border-t border-gray-100">
                    <p class="text-sm text-gray-500">{{ __('diwan.documents.qr') }}</p>
                    <div x-ref="qrContainer" class="p-4 bg-white border border-gray-200 rounded-lg">
                        {!! QrCode::size(200)->generate($document->verifyUrl()) !!}
                    </div>
                    <button type="button" @click="downloadQr()"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-sm font-medium rounded-md hover:bg-gray-50">
                        {{ __('diwan.documents.download_qr') }}
                    </button>
                </div>

                <div class="flex gap-3 pt-4">
                    <a href="{{ route('documents.stream', $document) }}" target="_blank"
                       class="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-700">
                        {{ __('diwan.documents.preview') }}
                    </a>
                    <a href="{{ route('documents.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-sm font-medium rounded-md hover:bg-gray-50">
                        {{ __('diwan.documents.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
