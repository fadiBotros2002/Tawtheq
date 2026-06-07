@props(['class' => '', 'align' => 'right'])

@php
    $current = app()->getLocale();
@endphp

<x-dropdown :align="$align" width="w-40" contentClasses="py-1 bg-white">
    <x-slot name="trigger">
        <button type="button"
                {{ $attributes->merge(['class' => 'inline-flex items-center justify-center rounded-lg p-2 text-gray-500 transition hover:bg-gray-100 hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 '.$class]) }}
                aria-label="{{ __('diwan.locale.switch') }}"
                title="{{ __('diwan.locale.switch') }}">
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 21l5.25-11.25L21 21m-9-3h7.5M3 5.621a48.474 48.474 0 0 1 6-.371m0 0c1.12 0 2.233.038 3.334.114M9 5.25V3m3.334 2.364C11.176 10.658 7.69 15.08 3 17.502m9.334-12.138c.896.061 1.785.147 2.666.257m-4.589 8.495a18.023 18.023 0 0 1-3.827-5.197" />
            </svg>
        </button>
    </x-slot>

    <x-slot name="content">
        <a href="{{ route('locale.switch', 'ar', absolute: false) }}"
           class="flex items-center gap-3 px-4 py-2.5 text-sm transition
                  {{ $current === 'ar' ? 'bg-indigo-50 font-medium text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}"
           @if ($current === 'ar') aria-current="true" @endif>
            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-xs font-bold
                         {{ $current === 'ar' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600' }}">
                ع
            </span>
            <span class="flex-1">{{ __('diwan.locale.ar') }}</span>
            @if ($current === 'ar')
                <svg class="h-4 w-4 shrink-0 text-indigo-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            @endif
        </a>

        <a href="{{ route('locale.switch', 'en', absolute: false) }}"
           class="flex items-center gap-3 px-4 py-2.5 text-sm transition
                  {{ $current === 'en' ? 'bg-indigo-50 font-medium text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}"
           @if ($current === 'en') aria-current="true" @endif>
            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-xs font-bold
                         {{ $current === 'en' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600' }}">
                EN
            </span>
            <span class="flex-1">{{ __('diwan.locale.en') }}</span>
            @if ($current === 'en')
                <svg class="h-4 w-4 shrink-0 text-indigo-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            @endif
        </a>
    </x-slot>
</x-dropdown>
