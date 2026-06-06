<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Correspondence Verification — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css'])
</head>
<body class="antialiased bg-gradient-to-br from-slate-50 to-indigo-50 min-h-screen">
    <div class="min-h-screen flex flex-col items-center justify-center px-4 py-8">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-100 mb-4">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">{{ config('app.name') }}</h1>
                <p class="text-gray-500 mt-1">Electronic Verification Portal</p>
            </div>

            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                @if ($correspondence->isApproved())
                    <div class="bg-emerald-500 px-6 py-4 text-center">
                        <div class="inline-flex items-center gap-2 text-white font-semibold">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Verified & Approved Correspondence
                        </div>
                    </div>
                @else
                    <div class="bg-amber-500 px-6 py-4 text-center">
                        <div class="inline-flex items-center gap-2 text-white font-semibold">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            Correspondence Pending Review
                        </div>
                    </div>
                @endif

                <div class="p-6 space-y-4">
                    @if ($correspondence->serial_number)
                        <div class="text-center pb-4 border-b border-gray-100">
                            <p class="text-xs text-gray-500 mb-1">Serial Number</p>
                            <p class="text-xl font-mono font-bold text-indigo-700">{{ $correspondence->serial_number }}</p>
                        </div>
                    @endif

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between items-start gap-4">
                            <span class="text-gray-500 shrink-0">Subject</span>
                            <span class="font-medium text-gray-900 text-right">{{ $correspondence->subject }}</span>
                        </div>
                        <div class="flex justify-between items-start gap-4">
                            <span class="text-gray-500 shrink-0">نوع الديوان</span>
                            <span class="font-medium text-gray-900">{{ $correspondence->category_label }}</span>
                        </div>
                        <div class="flex justify-between items-start gap-4">
                            <span class="text-gray-500 shrink-0">Sender</span>
                            <span class="font-medium text-gray-900">{{ $correspondence->sender }}</span>
                        </div>
                        <div class="flex justify-between items-start gap-4">
                            <span class="text-gray-500 shrink-0">Receiver</span>
                            <span class="font-medium text-gray-900">{{ $correspondence->receiver }}</span>
                        </div>
                        <div class="flex justify-between items-start gap-4">
                            <span class="text-gray-500 shrink-0">Status</span>
                            <span class="font-medium @if($correspondence->isApproved()) text-emerald-600 @else text-amber-600 @endif">
                                {{ $correspondence->status_label }}
                            </span>
                        </div>
                        @if ($correspondence->approved_at)
                            <div class="flex justify-between items-start gap-4">
                                <span class="text-gray-500 shrink-0">Approval Date</span>
                                <span class="font-medium text-gray-900">{{ $correspondenceData['approved_at'] }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="pt-4 border-t border-gray-100">
                        <p class="text-xs text-gray-400 text-center font-mono break-all">
                            {{ $correspondence->uuid }}
                        </p>
                    </div>
                </div>
            </div>

            <p class="text-center text-xs text-gray-400 mt-6">
                This page is for public verification only — no login required
            </p>
        </div>
    </div>
</body>
</html>
