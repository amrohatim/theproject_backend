@extends('layouts.dashboard')

@php
    $isArabic = app()->getLocale() === 'ar';
    $t = function ($ar, $en) use ($isArabic) {
        return $isArabic ? $ar : $en;
    };
@endphp

@section('title', $t('تفاصيل وظيفة مواطنات', 'Citizens Job Details'))
@section('page-title', $t('تفاصيل وظيفة مواطنات', 'Citizens Job Details'))

@section('content')
<div class="container mx-auto">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $job->title }}</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">{{ $t('وظائف مواطنات', 'Citizens Jobs') }}</p>
        </div>
        <div class="mt-4 md:mt-0 gap-4 flex items-center space-x-3">
            <a href="{{ route('vendor.citizens-jobs.applicants', $job->id) }}" class="inline-flex items-center px-4 py-2 bg-[var(--primary)] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[var(--primary-hover)] active:bg-[var(--primary)] focus:outline-none focus:border-[var(--primary)] focus:ring ring-[var(--primary)] disabled:opacity-25 transition ease-in-out duration-150">
                {{ $t('عرض المتقدمين', 'View Applicants') }}
            </a>
            <a href="{{ route('vendor.citizens-jobs.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-800 focus:outline-none focus:border-gray-700 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                {{ $t('رجوع', 'Back') }}
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $t('تفاصيل الوظيفة', 'Job Details') }}</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $t('النوع', 'Type') }}</div>
                    <div class="mt-1 text-gray-900 dark:text-white">{{ $job->type ?: '-' }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $t('الراتب', 'Salary') }}</div>
                    <div class="mt-1 text-gray-900 dark:text-white">{{ $job->salary ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $t('الموعد النهائي', 'Deadline') }}</div>
                    <div class="mt-1 text-gray-900 dark:text-white">{{ $job->deadline ? $job->deadline->format('Y-m-d') : '-' }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $t('حضوري', 'Onsite') }}</div>
                    <div class="mt-1 text-gray-900 dark:text-white">{{ $job->onsite ? $t('نعم', 'Yes') : $t('لا', 'No') }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $t('الموقع', 'Location') }}</div>
                    <div class="mt-1 text-gray-900 dark:text-white">{{ $job->location }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $t('عدد المتقدمين', 'Applicants') }}</div>
                    <div class="mt-1 text-gray-900 dark:text-white">{{ $job->number_of_applications }}</div>
                </div>
            </div>

            <div class="mt-6">
                <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $t('الوصف', 'Description') }}</div>
                <div class="mt-2 text-gray-900 dark:text-white whitespace-pre-line">{{ $job->description }}</div>
            </div>

            <div class="mt-6">
                <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $t('يفضل توفره', 'Nice to Have') }}</div>
                <div class="mt-2 text-gray-900 dark:text-white whitespace-pre-line">{{ $job->nice_to_have ?: '-' }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
