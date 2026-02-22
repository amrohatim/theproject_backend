@extends('layouts.dashboard')

@section('title', 'System Maintenance')
@section('page-title', 'System Maintenance')

@section('content')
<div class="container mx-auto">
    <div class="mb-6 flex items-start justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">System Maintenance</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">Control maintenance mode separately for web and mobile platforms.</p>
        </div>
        <a href="{{ route('admin.settings') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring ring-gray-300 transition ease-in-out duration-150">
            Back To Settings
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-md border border-green-200 bg-green-50 p-4 text-green-800 dark:border-green-800 dark:bg-green-900/20 dark:text-green-300">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 rounded-md border border-red-200 bg-red-50 p-4 text-red-800 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach(['web' => 'Web Platform', 'mobile' => 'Mobile Platform'] as $platformKey => $platformLabel)
            @php
                $maintenance = $maintenances[$platformKey] ?? null;
                $isEnabled = old('platform') === $platformKey ? old('maintenance', false) : ($maintenance?->maintenance ?? false);
                $message = old('platform') === $platformKey ? old('message') : ($maintenance?->message ?? '');
                $startAt = old('platform') === $platformKey
                    ? old('start_at')
                    : optional($maintenance?->start_at)->format('Y-m-d');
                $endAt = old('platform') === $platformKey
                    ? old('end_at')
                    : optional($maintenance?->end_at)->format('Y-m-d');
            @endphp

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $platformLabel }}</h3>
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $isEnabled ? 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300' : 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' }}">
                        {{ $isEnabled ? 'Maintenance ON' : 'Running Normally' }}
                    </span>
                </div>

                <form method="POST" action="{{ route('admin.settings.maintenance.upsert') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="platform" value="{{ $platformKey }}">

                    <div class="flex items-center justify-between rounded-md border border-gray-200 dark:border-gray-700 p-4">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Enable Maintenance</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Turn this on to stop {{ $platformKey }} usage during maintenance.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="maintenance" value="1" class="sr-only peer" {{ $isEnabled ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-red-300 dark:peer-focus:ring-red-800 dark:bg-gray-700 peer-checked:bg-red-600 after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                        </label>
                    </div>

                    <div>
                        <label for="message_{{ $platformKey }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Message</label>
                        <textarea
                            id="message_{{ $platformKey }}"
                            name="message"
                            rows="4"
                            required
                            class="block w-full p-2 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-red-500 focus:ring-red-500"
                 
                        >{{ $message }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="start_at_{{ $platformKey }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start At</label>
                            <input
                                type="date"
                                id="start_at_{{ $platformKey }}"
                                name="start_at"
                                required
                                value="{{ $startAt }}"
                                class="block w-full rounded-md p-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-red-500 focus:ring-red-500"
                            >
                        </div>

                        <div>
                            <label for="end_at_{{ $platformKey }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End At</label>
                            <input
                                type="date"
                                id="end_at_{{ $platformKey }}"
                                name="end_at"
                                required
                                value="{{ $endAt }}"
                                class="block w-full rounded-md border-gray-300 p-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-red-500 focus:ring-red-500"
                            >
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:ring ring-red-300 transition ease-in-out duration-150">
                            Save {{ $platformLabel }}
                        </button>
                    </div>
                </form>
            </div>
        @endforeach
    </div>
</div>
@endsection
