@extends('layouts.merchant')

@section('title', __('messages.jobs'))
@section('header', __('messages.jobs'))

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
                <h2 class="text-3xl font-bold text-gray-900">{{ __('messages.job_listings') }}</h2>
            </div>
            <p class="text-gray-600">{{ __('messages.manage_jobs') }}</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('merchant.jobs.create') }}" class="discord-btn">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                {{ __('messages.add_job') }}
            </a>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="flex items-center justify-between p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">{{ __('messages.jobs') }}</h3>
        <span class="px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded-full">{{ $jobs->total() }}</span>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.title') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.job_type') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.deadline') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.onsite') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.location') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.applications') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($jobs as $job)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ \Illuminate\Support\Str::limit($job->title, 60) }}</div>
                            <div class="text-xs text-gray-500">{{ \Illuminate\Support\Str::limit($job->description, 90) }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $job->type_other ?: ($job->category->name ?? '-') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $job->deadline ? $job->deadline->format('Y-m-d') : '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $job->onsite ? __('messages.yes') : __('messages.no') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $job->location }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $job->number_of_applications }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end space-x-3">
                                <a href="{{ route('merchant.jobs.show', $job->id) }}" class="inline-flex items-center px-3 py-1 text-xs font-semibold text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                                    View
                                </a>
                                @if($job->number_of_applications > 0)
                                    <a href="{{ route('merchant.jobs.applicants', $job->id) }}" class="inline-flex items-center px-3 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-md hover:bg-blue-200">
                                        View Applicants
                                    </a>
                                @endif
                                <form method="POST" action="{{ route('merchant.jobs.destroy', $job->id) }}" onsubmit="return confirm('{{ __('messages.confirm_delete') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                        {{ __('messages.delete') }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">
                            {{ __('messages.no_jobs_found') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($jobs->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $jobs->links() }}
        </div>
    @endif
</div>
@endsection
