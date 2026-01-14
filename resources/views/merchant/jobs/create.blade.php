@extends('layouts.merchant')

@section('title', __('messages.add_job'))
@section('header', __('messages.add_job'))

@section('content')
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center space-x-3 mb-2">
                <div class="p-2 bg-blue-100 rounded-md">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m2 0a2 2 0 012 2v6H5v-6a2 2 0 012-2m2-7h6m-6 0V7a2 2 0 012-2h2a2 2 0 012 2v2"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-900">{{ __('messages.post_job') }}</h2>
            </div>
            <p class="text-gray-600">{{ __('messages.manage_jobs') }}</p>
        </div>
    </div>
</div>

@if ($errors->any())
    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 p-3 rounded">
        <ul class="list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <form method="POST" action="{{ route('merchant.jobs.store') }}" class="space-y-4">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="title">{{ __('messages.title') }}</label>
                <input id="title" name="title" type="text" value="{{ old('title') }}" required class="w-full rounded-md p-2 border border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="salary">{{ __('messages.salary') }}  ({{ __('messages.optional') }})</label>
                <input id="salary" name="salary" type="number" min="0" value="{{ old('salary') }}" class="w-full rounded-md p-2 border border-gray-300">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1" for="description">{{ __('messages.description') }}</label>
            <textarea id="description" name="description" rows="4" required class="w-full rounded-md p-2 border border-gray-300">{{ old('description') }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="nice_to_have">{{ __('messages.nice_to_have') }}  ({{ __('messages.optional') }})</label>
                <textarea id="nice_to_have" name="nice_to_have" rows="3" class="w-full rounded-md p-2 border border-gray-300">{{ old('nice_to_have') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="deadline">{{ __('messages.deadline') }}  ({{ __('messages.optional') }})</label>
                <input id="deadline" name="deadline" type="date" value="{{ old('deadline') }}" class="w-full rounded-md p-2 border border-gray-300">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="type">{{ __('messages.job_type') }}</label>
                <select id="type" name="type" required class="w-full rounded-md p-2 border border-gray-300">
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
                <label class="block text-sm font-medium text-gray-700 mb-1" for="type_other">{{ __('messages.job_type_other') }}</label>
                <input id="type_other" name="type_other" type="text" value="{{ old('type_other') }}" class="w-full rounded-md p-2 border border-gray-300">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="location">{{ __('messages.location') }}</label>
                <input id="location" name="location" type="text" value="{{ old('location') }}" required class="w-full rounded-md p-2 border border-gray-300">
            </div>
            <div class="flex items-center gap-2 pt-6">
                <input type="checkbox" name="onsite" value="1" id="onsite" {{ old('onsite') ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <label for="onsite" class="text-sm text-gray-700">{{ __('messages.onsite') }}</label>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="discord-btn">
                {{ __('messages.save_job') }}
            </button>
        </div>
    </form>
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
