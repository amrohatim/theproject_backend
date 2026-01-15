@extends('layouts.merchant')

@section('title', 'Job Details')
@section('header', 'Job Details')

@section('content')
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center space-x-3 mb-2">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m2 0a2 2 0 012 2v6H5v-6a2 2 0 012-2m2-7h6m-6 0V7a2 2 0 012-2h2a2 2 0 012 2v2"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-900">{{ $job->title }}</h2>
            </div>
            <p class="text-gray-600">{{ __('messages.job_listings') }}</p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
            @if($job->number_of_applications > 0)
                <a href="{{ route('merchant.jobs.applicants', $job->id) }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded-md hover:bg-blue-700">
                    View Applicants
                </a>
            @endif
            <a href="{{ route('merchant.jobs.index') }}" class="discord-btn">
                Back to Jobs
            </a>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="flex items-center justify-between p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Job Details</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('messages.job_type') }}</div>
                <div class="mt-1 text-gray-900">{{ $job->type_other ?: ($job->category->name ?? '-') }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('messages.salary') }}</div>
                <div class="mt-1 text-gray-900">{{ $job->salary ?? '-' }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('messages.deadline') }}</div>
                <div class="mt-1 text-gray-900">{{ $job->deadline ? $job->deadline->format('Y-m-d') : '-' }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('messages.onsite') }}</div>
                <div class="mt-1 text-gray-900">{{ $job->onsite ? __('messages.yes') : __('messages.no') }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('messages.location') }}</div>
                <div class="mt-1 text-gray-900">{{ $job->location }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('messages.applications') }}</div>
                <div class="mt-1 text-gray-900">{{ $job->number_of_applications }}</div>
            </div>
        </div>

        <div class="mt-6">
            <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('messages.description') }}</div>
            <div class="mt-2 text-gray-900 whitespace-pre-line">{{ $job->description }}</div>
        </div>

        <div class="mt-6">
            <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('messages.nice_to_have') }}</div>
            <div class="mt-2 text-gray-900 whitespace-pre-line">{{ $job->nice_to_have ?: '-' }}</div>
        </div>
    </div>
</div>
@endsection
