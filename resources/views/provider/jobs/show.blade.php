@extends('layouts.dashboard')

@section('title', __('messages.job_details') == 'messages.job_details' ? 'Job Details' : __('messages.job_details'))
@section('page-title', __('messages.job_details') == 'messages.job_details' ? 'Job Details' : __('messages.job_details'))

@section('content')
<div class="container mx-auto">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $job->title }}</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">{{ __('messages.job_listings') }}</p>
        </div>
        <div class="mt-4 md:mt-0 flex items-center space-x-3">
            @if($job->number_of_applications > 0)
                <a href="{{ route('provider.jobs.applicants', $job->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                    {{ __('messages.view_applicants') ?? 'View Applicants' }}
                </a>
            @endif
            <a href="{{ route('provider.jobs.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-800 focus:outline-none focus:border-gray-700 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                {{ __('messages.back_to_jobs') ?? 'Back to Jobs' }}
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('messages.job_details') == 'messages.job_details' ? 'Job Details' : __('messages.job_details') }}</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('messages.job_type') }}</div>
                    <div class="mt-1 text-gray-900 dark:text-white">{{ $job->type_other ?: ($job->category->name ?? '-') }}</div>
                </div>
                <div>
                    <div class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('messages.salary') }}</div>
                    <div class="mt-1 text-gray-900 dark:text-white">{{ $job->salary ?? '-' }}</div>
                </div>
                    <div>
                        <div class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('messages.deadline') }}</div>
                    <div class="mt-1 text-gray-900 dark:text-white">{{ $job->deadline ? $job->deadline->format('Y-m-d') : '-' }}</div>
                </div>
                <div>
                    <div class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('messages.onsite') }}</div>
                    <div class="mt-1 text-gray-900 dark:text-white">{{ $job->onsite ? __('messages.yes') : __('messages.no') }}</div>
                </div>
                <div>
                    <div class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('messages.location') }}</div>
                    <div class="mt-1 text-gray-900 dark:text-white">{{ $job->location }}</div>
                </div>
                <div>
                    <div class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('messages.applications') }}</div>
                    <div class="mt-1 text-gray-900 dark:text-white">{{ $job->number_of_applications }}</div>
                </div>
            </div>

            <div class="mt-6">
                <div class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('messages.description') }}</div>
                <div class="mt-2 text-gray-900 dark:text-white whitespace-pre-line">{{ $job->description }}</div>
            </div>

            <div class="mt-6">
                <div class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('messages.nice_to_have') }}</div>
                <div class="mt-2 text-gray-900 dark:text-white whitespace-pre-line">{{ $job->nice_to_have ?: '-' }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
