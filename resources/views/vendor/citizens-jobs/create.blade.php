@extends('layouts.dashboard')

@php
    $isArabic = app()->getLocale() === 'ar';
    $t = function ($ar, $en) use ($isArabic) {
        return $isArabic ? $ar : $en;
    };
    $dir = in_array(app()->getLocale(), ['ar']) ? 'rtl' : 'ltr';
@endphp

@section('title', $t('إضافة وظيفة مواطنات', 'Add Citizens Job'))
@section('page-title', $t('إضافة وظيفة مواطنات', 'Add Citizens Job'))

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">{{ $t('نشر وظيفة مواطنات', 'Post Citizens Job') }}</h2>

        @if ($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 p-3 rounded">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('vendor.citizens-jobs.store') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="title">{{ $t('العنوان', 'Title') }}</label>
                    <input id="title" name="title" type="text" value="{{ old('title') }}" required class="{{ $dir === 'rtl' ? 'px-8' : 'px-4' }} w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="salary">{{ $t('الراتب', 'Salary') }}</label>
                    <input id="salary" name="salary" type="number" min="0" value="{{ old('salary') }}" required class="{{ $dir === 'rtl' ? 'px-8' : 'px-4' }} w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="description">{{ $t('الوصف', 'Description') }}</label>
                <textarea id="description" name="description" rows="4" required class="w-full rounded-md border-gray-300 px-4 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="nice_to_have">{{ $t('المتطلبات الإضافية', 'Additional Requirements') }}</label>
                <textarea id="nice_to_have" name="nice_to_have" rows="3" required class="w-full rounded-md border-gray-300 px-4 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">{{ old('nice_to_have') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="phone_number">{{ $t('رقم الهاتف (اختياري)', 'Phone (optional)') }}</label>
                    <input id="phone_number" name="phone_number" type="text" value="{{ old('phone_number') }}" class="{{ $dir === 'rtl' ? 'px-8' : 'px-4' }} w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="email">{{ $t('البريد الإلكتروني (اختياري)', 'Email (optional)') }}</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" class="{{ $dir === 'rtl' ? 'px-8' : 'px-4' }} w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="deadline">{{ $t('الموعد النهائي', 'Deadline') }}</label>
                    <input id="deadline" name="deadline" type="date" value="{{ old('deadline') }}" required class="w-full rounded-md border-gray-300 px-4 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="type">{{ $t('النوع', 'Type') }}</label>
                    <input id="type" name="type" type="text" value="{{ old('type') }}" required class="{{ $dir === 'rtl' ? 'px-8' : 'px-4' }} w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="location">{{ $t('الموقع', 'Location') }}</label>
                    <input id="location" name="location" type="text" value="{{ old('location') }}" required class="{{ $dir === 'rtl' ? 'px-8' : 'px-4' }} w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                </div>
                <div class="flex items-center gap-2 pt-6">
                    <input type="checkbox" name="onsite" value="1" {{ old('onsite') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $t('حضوري', 'Onsite') }}</span>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('vendor.citizens-jobs.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                    {{ $t('رجوع', 'Back') }}
                </a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    {{ $t('حفظ وظيفة مواطنات', 'Save Citizens Job') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
