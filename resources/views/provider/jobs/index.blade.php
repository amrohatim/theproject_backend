@extends('layouts.provider')

@section('title', __('messages.jobs'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
        <div style="width: 40px; height: 40px; background-color: var(--discord-primary); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
            <i class="fas fa-briefcase text-white"></i>
        </div>
        <div>
            <h4 class="mb-0">{{ __('messages.job_listings') }}</h4>
            <p class="text-muted mb-0" style="font-size: 14px; color: var(--discord-light);">{{ __('messages.manage_jobs') }}</p>
        </div>
    </div>

    <a href="{{ route('provider.jobs.create') }}" class="discord-btn">
        <i class="fas fa-plus me-2"></i> {{ __('messages.add_job') }}
    </a>
</div>

<div class="discord-card">
    <div class="discord-card-header">
        <i class="fas fa-list me-2" style="color: var(--discord-primary);"></i>
        {{ __('messages.jobs') }}
        <span class="badge" style="background-color: var(--discord-primary); color: white; font-size: 12px; padding: 4px 8px; border-radius: 20px; margin-left: 10px;">{{ $jobs->total() }}</span>
    </div>

    <div class="table-responsive">
        <table class="table" style="color: var(--discord-lightest); margin-bottom: 0;">
            <thead>
                <tr>
                    <th>{{ __('messages.title') }}</th>
                    <th>{{ __('messages.job_type') }}</th>
                    <th>{{ __('messages.deadline') }}</th>
                    <th>{{ __('messages.onsite') }}</th>
                    <th>{{ __('messages.location') }}</th>
                    <th>{{ __('messages.applications') }}</th>
                    <th class="text-end">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jobs as $job)
                    <tr>
                        <td>
                            <div style="font-weight: 600;">{{ \Illuminate\Support\Str::limit($job->title, 60) }}</div>
                            <div style="font-size: 12px; color: var(--discord-light);">
                                {{ \Illuminate\Support\Str::limit($job->description, 90) }}
                            </div>
                        </td>
                        <td>{{ $job->type_other ?: ($job->category->name ?? '-') }}</td>
                        <td>{{ $job->deadline ? $job->deadline->format('Y-m-d') : '-' }}</td>
                        <td>{{ $job->onsite ? __('messages.yes') : __('messages.no') }}</td>
                        <td>{{ $job->location }}</td>
                        <td>{{ $job->number_of_applications }}</td>
                        <td class="text-end">
                            <form method="POST" action="{{ route('provider.jobs.destroy', $job->id) }}" onsubmit="return confirm('{{ __('messages.confirm_delete') }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-link p-0 text-danger">
                                    <i class="fas fa-trash me-1"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 32px 16px;">
                            <div style="color: var(--discord-light);">{{ __('messages.no_jobs_found') }}</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($jobs->hasPages())
        <div class="mt-3">
            {{ $jobs->links() }}
        </div>
    @endif
</div>
@endsection
