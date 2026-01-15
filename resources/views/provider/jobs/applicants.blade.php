@extends('layouts.provider')

@section('title', 'Applicants')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
        <div style="width: 40px; height: 40px; background-color: var(--discord-primary); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
            <i class="fas fa-users text-white"></i>
        </div>
        <div>
            <h4 class="mb-0">Applicants</h4>
            <p class="text-muted mb-0" style="font-size: 14px; color: var(--discord-light);">{{ $job->title }}</p>
        </div>
    </div>

    <a href="{{ route('provider.jobs.index') }}" class="discord-btn">
        Back to Jobs
    </a>
</div>

<div class="discord-card">
    <div class="discord-card-header">
        <i class="fas fa-users me-2" style="color: var(--discord-primary);"></i>
        Applicants
        <span class="badge" style="background-color: var(--discord-primary); color: white; font-size: 12px; padding: 4px 8px; border-radius: 20px; margin-left: 10px;">{{ $applications->count() }}</span>
    </div>

    <div class="table-responsive">
        <table class="table" style="color: var(--discord-lightest); margin-bottom: 0;">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $application)
                    <tr>
                        <td>{{ $application->user_name }}</td>
                        <td>{{ $application->user_email }}</td>
                        <td>{{ $application->user_phone }}</td>
                        <td>{{ $application->user_address }}</td>
                        <td class="text-end">
                            <div class="d-inline-flex align-items-center gap-2">
                                @if($application->user_cv)
                                    <a href="{{ route('provider.jobs.applicants.cv', $application->id) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-success">
                                        Show CV
                                    </a>
                                @endif
                                <form method="POST" action="{{ route('provider.jobs.applicants.destroy', $application->id) }}" onsubmit="return confirm('Delete this application?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center" style="padding: 32px 16px;">
                            <div style="color: var(--discord-light);">No applicants found.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
