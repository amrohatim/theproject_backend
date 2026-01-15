@extends('layouts.merchant')

@section('title', 'Applicants')
@section('header', 'Applicants')

@section('content')
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center space-x-3 mb-2">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-900">Applicants</h2>
            </div>
            <p class="text-gray-600">{{ $job->title }}</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('merchant.jobs.index') }}" class="discord-btn">
                Back to Jobs
            </a>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="flex items-center justify-between p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Applicants</h3>
        <span class="px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded-full">{{ $applications->count() }}</span>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($applications as $application)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $application->user_name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $application->user_email }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $application->user_phone }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $application->user_address }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end space-x-3">
                                @if($application->user_cv)
                                    <a href="{{ route('merchant.jobs.applicants.cv', $application->id) }}" target="_blank" rel="noopener" class="inline-flex items-center px-3 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-md hover:bg-green-200">
                                        Show CV
                                    </a>
                                @endif
                                <form method="POST" action="{{ route('merchant.jobs.applicants.destroy', $application->id) }}" onsubmit="return confirm('Delete this application?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-3 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-md hover:bg-red-200">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">
                            No applicants found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
