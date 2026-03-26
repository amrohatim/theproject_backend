@extends('layouts.dashboard')

@section('title', __('messages.select_branch'))
@section('page-title', __('messages.select_branch'))

@section('content')
<div class="container mx-auto">
    <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('messages.select_branch') }}</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">{{ __('messages.choose_branch_for_service_creation') }}</p>
        </div>
        <div>
            <a href="{{ route('vendor.services.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-right mx-2' : 'fa-arrow-left mx-2' }} text-xs"></i> {{ __('messages.back') }}
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('messages.available_branches') }}</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('messages.branch_selection_prefill_notice') }}</p>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach($branches as $branch)
                <a
                    href="{{ route('vendor.services.create', ['branch_id' => $branch->id]) }}"
                    class="group rounded-xl border border-gray-200 bg-gray-50 p-5 transition hover:border-[var(--primary)] dark:border-gray-700 dark:bg-gray-900/40 dark:hover:border-[var(--primary)] dark:hover:bg-gray-700/70"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h4 class="text-base font-semibold text-gray-900 group-hover:text-[var(--primary)] dark:text-white dark:group-hover:text-[var(--primary)]">
                                {{ $branch->name }}
                            </h4>
                            @if($branch->business_type)
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ $branch->business_type }}</p>
                            @endif
                        </div>
                        <span class="inline-flex h-20 w-20 items-center justify-center overflow-hidden rounded-full bg-[var(--primary)] text-[var(--primary)] dark:bg-[var(--primary)]/50 dark:text-[var(--primary)]">
                            @if($branch->branch_image)
                                <img src="{{ $branch->branch_image }}" alt="{{ $branch->name }} Logo" class="h-full w-full rounded-full object-cover">
                            @else
                                <i class="fas fa-store"></i>
                            @endif
                        </span>
                    </div>

                    @if($branch->address || $branch->emirate)
                        <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                            @if($branch->address)
                                <p>{{ $branch->address }}</p>
                            @endif
                            @if($branch->emirate)
                                <p class="mt-1">{{ $branch->emirate }}</p>
                            @endif
                        </div>
                    @endif

                    <div class="mt-5 inline-flex items-center text-sm font-medium text-[var(--primary)] dark:text-[var(--primary)]">
                        {{ __('messages.continue_to_service_form') }}
                        <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-left mx-2' : 'fa-arrow-right mx-2' }} text-xs"></i>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>
@endsection
