@extends('layouts.dashboard')

@section('title', __('messages.add_job'))
@section('page-title', __('messages.add_job'))

@section('styles')
<style>
    [dir="rtl"] .onsite-check {
        flex-direction: row-reverse;
        display: inline-flex;
        align-items: center;
        gap: 12px;
    }
    .provider-jobs-form input,
    .provider-jobs-form textarea,
    .provider-jobs-form select {
        padding: 0.65rem 0.9rem;
    }
</style>
@endsection

@section('content')
<div class="container mx-auto">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ __('messages.post_job') }}</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('messages.manage_jobs') }}</p>
        </div>
        <a href="{{ route('provider.jobs.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
            <i class="fas fa-arrow-left mr-2"></i> {{ __('messages.back') ?? 'Back' }}
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <div class="flex items-center gap-2 font-semibold">
                <i class="fas fa-exclamation-circle"></i>
                <span>{{ __('messages.error') }}</span>
            </div>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <form method="POST" action="{{ route('provider.jobs.store') }}" class="space-y-6 provider-jobs-form">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.title') }}</label>
                    <input id="title" name="title" type="text" value="{{ old('title') }}" required
                           class="mt-2 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label for="salary" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.salary') }} ({{ __('messages.optional') }})</label>
                    <input id="salary" name="salary" type="number" min="0" value="{{ old('salary') }}"
                           class="mt-2 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.description') }}</label>
                <textarea id="description" name="description" rows="4" required
                          class="mt-2 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 resize-y">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="nice_to_have" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.nice_to_have') }} ({{ __('messages.optional') }})</label>
                    <textarea id="nice_to_have" name="nice_to_have" rows="3"
                              class="mt-2 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 resize-y">{{ old('nice_to_have') }}</textarea>
                </div>
                <div>
                    <label for="deadline" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.deadline') }} ({{ __('messages.optional') }})</label>
                    <input id="deadline" name="deadline" type="date" value="{{ old('deadline') }}"
                           class="mt-2 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.job_type') }}</label>
                    <select id="type" name="type" required
                            class="mt-2 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="" disabled {{ old('type') ? '' : 'selected' }}>{{ __('messages.select_job_type') }}</option>
                        @foreach ($parentCategories as $category)
                            <option value="{{ $category->id }}" {{ old('type') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                        <option value="other" {{ old('type') === 'other' ? 'selected' : '' }}>{{ __('messages.other') }}</option>
                    </select>
                </div>
                <div id="type-other-wrapper" style="display: none;">
                    <label for="type_other" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.job_type_other') }}</label>
                    <input id="type_other" name="type_other" type="text" value="{{ old('type_other') }}"
                           class="mt-2 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.location') }}</label>
                    <input id="location" name="location" type="text" value="{{ old('location') }}" required
                           class="mt-2 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="flex items-end">
                    <div class="onsite-check">
                        <input class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" type="checkbox" name="onsite" value="1" id="onsite" {{ old('onsite') ? 'checked' : '' }}>
                        <label class="text-sm text-gray-700 dark:text-gray-300" for="onsite">{{ __('messages.onsite') }}</label>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-save mr-2"></i> {{ __('messages.save_job') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const typeSelect = document.getElementById('type');
    const typeOtherWrapper = document.getElementById('type-other-wrapper');
    const typeOtherInput = document.getElementById('type_other');

    function toggleTypeOther() {
        const isOther = typeSelect.value === 'other';
        typeOtherWrapper.style.display = isOther ? 'block' : 'none';
        typeOtherInput.required = isOther;
        if (!isOther) {
            typeOtherInput.value = '';
        }
    }

    typeSelect.addEventListener('change', toggleTypeOther);
    toggleTypeOther();
</script>
@endsection
