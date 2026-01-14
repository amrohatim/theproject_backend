@extends('layouts.dashboard')

@section('title', __('messages.add_job'))
@section('page-title', __('messages.add_job'))

@php
    $dir = in_array(app()->getLocale(), ['ar']) ? 'rtl' : 'ltr';
@endphp
@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">{{ __('messages.post_job') }}</h2>

        @if ($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 p-3 rounded">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('vendor.jobs.store') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="title">{{ __('messages.title') }}</label>
                    <input id="title" name="title" type="text" value="{{ old('title') }}" required class="{{ $dir === 'rtl' ? 'px-8' : 'px-4' }} w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="salary">{{ __('messages.salary') }} ({{ __('messages.optional') }})</label>
                    <input id="salary" name="salary" type="number" min="0" value="{{ old('salary') }}" class="{{ $dir === 'rtl' ? 'px-8' : 'px-4' }} w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="description">{{ __('messages.description') }}</label>
                <textarea id="description" name="description" rows="4" required class="w-full rounded-md border-gray-300 px-4 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="nice_to_have">{{ __('messages.nice_to_have') }} ({{ __('messages.optional') }})</label>
                    <textarea id="nice_to_have" name="nice_to_have" rows="3" class="w-full rounded-md border-gray-300 px-4 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">{{ old('nice_to_have') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="deadline">{{ __('messages.deadline') }} ({{ __('messages.optional') }})</label>
                    <input id="deadline" name="deadline" type="date" value="{{ old('deadline') }}" class="w-full rounded-md border-gray-300 px-4 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="type">{{ __('messages.job_type') }}</label>
                    <select id="type" name="type" required class="w-full rounded-md border-gray-300 px-4 py-3 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
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
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="type_other">{{ __('messages.job_type_other') }}</label>
                    <input id="type_other" name="type_other" type="text" value="{{ old('type_other') }}" class="w-full px-4 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="location">{{ __('messages.location') }}</label>
                    <input id="location" name="location" type="text" value="{{ old('location') }}" required class="{{ $dir === 'rtl' ? 'px-8' : 'px-4' }} w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                </div>
                <div class="flex items-center gap-2 pt-6">
                    <input type="checkbox" name="onsite" value="1" {{ old('onsite') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('messages.onsite') }}</span>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    {{ __('messages.save_job') }}
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
