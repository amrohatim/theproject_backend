@extends('layouts.dashboard')

@php
    $isArabic = app()->getLocale() === 'ar';
    $t = function ($ar, $en) use ($isArabic) {
        return $isArabic ? $ar : $en;
    };
@endphp

@section('title', $t('وظائف مواطنات', 'Citizens Jobs'))
@section('page-title', $t('وظائف مواطنات', 'Citizens Jobs'))

@section('styles')
<style>
    [dir="rtl"] .jobs-header {
        text-align: right;
    }

    [dir="rtl"] .jobs-table th,
    [dir="rtl"] .jobs-table td {
        text-align: right;
    }
</style>
@endsection

@section('content')
<div class="container mx-auto">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between jobs-header">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $t('وظائف مواطنات', 'Citizens Jobs') }}</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">{{ $t('إدارة وظائف المواطنات', 'Manage citizens jobs') }}</p>
        </div>
        <div class="mt-4 md:mt-0 jobs-actions flex md:justify-end">
            <a href="{{ route('vendor.citizens-jobs.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i> {{ $t('إضافة وظيفة مواطنات', 'Add Citizens Job') }}
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 jobs-table">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ $t('العنوان', 'Title') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ $t('النوع', 'Type') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ $t('الموعد النهائي', 'Deadline') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ $t('حضوري', 'Onsite') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ $t('الموقع', 'Location') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ $t('عدد المتقدمين', 'Applicants') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ $t('الإجراءات', 'Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($jobs as $job)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $job->title }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ \Illuminate\Support\Str::limit($job->description, 80) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $job->type ?: '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $job->deadline ? $job->deadline->format('Y-m-d') : '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $job->onsite ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-200' }}">
                                    {{ $job->onsite ? $t('نعم', 'Yes') : $t('لا', 'No') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $job->location }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $job->number_of_applications }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('vendor.citizens-jobs.show', $job->id) }}" class="inline-flex items-center px-3 py-1 text-xs font-semibold text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                                        {{ $t('عرض', 'View') }}
                                    </a>
                                    <a href="{{ route('vendor.citizens-jobs.applicants', $job->id) }}" class="inline-flex items-center px-3 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-md hover:bg-blue-200">
                                        {{ $t('عرض المتقدمين', 'View Applicants') }}
                                    </a>
                                    <form method="POST" action="{{ route('vendor.citizens-jobs.destroy', $job->id) }}" onsubmit="return confirm('{{ $t('هل أنت متأكد من حذف هذه الوظيفة؟', 'Are you sure you want to delete this job?') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center text-sm font-medium text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash mr-1"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-briefcase text-gray-300 dark:text-gray-600 text-5xl mb-4"></i>
                                    <p>{{ $t('لا توجد وظائف مواطنات.', 'No citizens jobs found.') }}</p>
                                    <a href="{{ route('vendor.citizens-jobs.create') }}" class="mt-3 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                        <i class="fas fa-plus mr-2"></i> {{ $t('إضافة وظيفة مواطنات', 'Add Citizens Job') }}
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($jobs->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $jobs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
