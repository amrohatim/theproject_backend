@extends('layouts.provider')

@section('title', 'Job Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
        <div style="width: 40px; height: 40px; background-color: var(--discord-primary); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
            <i class="fas fa-briefcase text-white"></i>
        </div>
        <div>
            <h4 class="mb-0">{{ $job->title }}</h4>
            <p class="text-muted mb-0" style="font-size: 14px; color: var(--discord-light);">{{ __('messages.job_listings') }}</p>
        </div>
    </div>

    <div class="d-flex align-items-center gap-2">
        @if($job->number_of_applications > 0)
            <a href="{{ route('provider.jobs.applicants', $job->id) }}" class="btn btn-sm btn-outline-primary">
                View Applicants
            </a>
        @endif
        <a href="{{ route('provider.jobs.index') }}" class="discord-btn">
            Back to Jobs
        </a>
    </div>
</div>

<div class="discord-card">
    <div class="discord-card-header">
        <i class="fas fa-info-circle me-2" style="color: var(--discord-primary);"></i>
        Job Details
    </div>

    <div class="table-responsive">
        <table class="table" style="color: var(--discord-lightest); margin-bottom: 0;">
            <tbody>
                <tr>
                    <th style="width: 220px;">{{ __('messages.title') }}</th>
                    <td>{{ $job->title }}</td>
                </tr>
                <tr>
                    <th>{{ __('messages.description') }}</th>
                    <td>{{ $job->description }}</td>
                </tr>
                <tr>
                    <th>{{ __('messages.job_type') }}</th>
                    <td>{{ $job->type_other ?: ($job->category->name ?? '-') }}</td>
                </tr>
                <tr>
                    <th>{{ __('messages.salary') }}</th>
                    <td>{{ $job->salary ?? '-' }}</td>
                </tr>
                <tr>
                    <th>{{ __('messages.nice_to_have') }}</th>
                    <td>{{ $job->nice_to_have ?: '-' }}</td>
                </tr>
                <tr>
                    <th>{{ __('messages.deadline') }}</th>
                    <td>{{ $job->deadline ? $job->deadline->format('Y-m-d') : '-' }}</td>
                </tr>
                <tr>
                    <th>{{ __('messages.onsite') }}</th>
                    <td>{{ $job->onsite ? __('messages.yes') : __('messages.no') }}</td>
                </tr>
                <tr>
                    <th>{{ __('messages.location') }}</th>
                    <td>{{ $job->location }}</td>
                </tr>
                <tr>
                    <th>{{ __('messages.applications') }}</th>
                    <td>{{ $job->number_of_applications }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
