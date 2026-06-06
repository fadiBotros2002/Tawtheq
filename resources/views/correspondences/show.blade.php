<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                View Correspondence
            </h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('correspondences.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                    Back to Log
                </a>
                @if ($correspondence->isApproved())
                    <button type="button" onclick="window.print()"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition print:hidden">
                        <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Print
                    </button>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8 print:py-0">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg print:hidden">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg print:hidden">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden print:shadow-none print:rounded-none">
                <div class="border-b-4 border-indigo-600 px-8 py-6 bg-gradient-to-r from-indigo-50 to-white">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                        <div>
                            <p class="text-sm text-indigo-600 font-medium mb-1">{{ config('app.name') }}</p>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $correspondence->subject }}</h1>
                        </div>
                        <div class="text-right sm:text-right space-y-1">
                            @if ($correspondence->serial_number)
                                <p class="text-sm text-gray-600">Serial Number</p>
                                <p class="text-lg font-mono font-bold text-indigo-700">{{ $correspondence->serial_number }}</p>
                            @else
                                <p class="text-sm text-amber-600 font-medium">Awaiting Approval</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="px-8 py-6 space-y-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-500 mb-1">نوع الديوان</p>
                            <p class="font-semibold text-gray-900">{{ $correspondence->category_label }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-500 mb-1">Priority</p>
                            <p class="font-semibold @if($correspondence->priority === 'urgent') text-red-600 @else text-gray-900 @endif">
                                {{ $correspondence->priority_label }}
                            </p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-500 mb-1">Status</p>
                            <p class="font-semibold @if($correspondence->isApproved()) text-emerald-600 @else text-amber-600 @endif">
                                {{ $correspondence->status_label }}
                            </p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-500 mb-1">Date</p>
                            <p class="font-semibold text-gray-900">{{ $correspondenceData['created_at'] }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-gray-100 pt-6">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Sender</p>
                            <p class="text-lg font-medium text-gray-900">{{ $correspondence->sender }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Receiver</p>
                            <p class="text-lg font-medium text-gray-900">{{ $correspondence->receiver }}</p>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-6">
                        <p class="text-sm text-gray-500 mb-3">Correspondence Text</p>
                        <div class="prose prose-sm max-w-none text-gray-800 leading-relaxed whitespace-pre-wrap bg-gray-50 rounded-lg p-6 border border-gray-100">
                            {{ $correspondence->content }}
                        </div>
                    </div>

                    @if ($correspondence->file_path)
                        <div class="border-t border-gray-100 pt-6 print:hidden">
                            <p class="text-sm text-gray-500 mb-2">Attachment</p>
                            <a href="{{ $correspondenceData['file_url'] }}" target="_blank"
                               class="inline-flex items-center px-4 py-2 bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition text-sm font-medium">
                                <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Download Attachment
                            </a>
                        </div>
                    @endif

                    @if ($correspondence->isApproved())
                        <div class="border-t border-gray-200 pt-8 flex flex-col items-center justify-center text-center">
                            <p class="text-sm text-gray-500 mb-3">Scan the code to verify this correspondence</p>
                            <img src="{{ $correspondenceData['qr_code_url'] }}"
                                 alt="Verification QR Code"
                                 class="w-[150px] h-[150px] border border-gray-200 rounded-lg p-2 bg-white">
                            <p class="text-xs text-gray-400 mt-2 font-mono">{{ $correspondenceData['verify_url'] }}</p>
                            @if ($correspondence->approved_at)
                                <p class="text-xs text-gray-500 mt-2">Approval Date: {{ $correspondenceData['approved_at'] }}</p>
                            @endif
                        </div>
                    @endif
                </div>

                @if (! $correspondence->isApproved() && auth()->user()->isChecker())
                    <div class="px-8 py-6 bg-gray-50 border-t border-gray-100 print:hidden">
                        <form method="POST" action="{{ route('correspondences.approve', $correspondence) }}"
                              onsubmit="return confirm('Are you sure you want to approve this correspondence? It cannot be edited after approval.');">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center px-6 py-3 bg-emerald-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition">
                                <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Approve Correspondence & Issue Serial Number
                            </button>
                        </form>
                    </div>
                @endif

                @if ($correspondence->isApproved())
                    <div class="px-8 py-4 bg-emerald-50 border-t border-emerald-100 print:hidden">
                        <p class="text-sm text-emerald-800 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            This correspondence is approved and locked — it cannot be edited
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        @media print {
            nav, header button, .print\\:hidden { display: none !important; }
            body { background: white !important; }
        }
    </style>
</x-app-layout>
