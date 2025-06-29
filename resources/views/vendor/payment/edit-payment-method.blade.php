@extends('layouts.vendor')

@section('title', 'Edit Payment Method')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center mb-6">
        <a href="{{ route('vendor.payment.index') }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Edit Payment Method</h1>
    </div>

    @if($errors->any())
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
        <p class="font-bold">Please fix the following errors:</p>
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
        <div class="p-6">
            <form action="{{ route('vendor.payment.methods.update', $paymentMethod->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="payment_type" value="{{ $paymentMethod->payment_type }}">
                <input type="hidden" name="provider_type" value="{{ $paymentMethod->provider_type }}">
                <input type="hidden" name="is_verified" value="{{ $paymentMethod->is_verified ? 1 : 0 }}">
                <input type="hidden" name="verified_at" value="{{ $paymentMethod->verified_at ?? now() }}">

                @if($paymentMethod->payment_type === 'credit_card')
                <div class="mb-4">
                    <div class="flex items-center mb-4">
                        @if($paymentMethod->card_brand === 'visa')
                        <div class="w-12 h-8 bg-blue-600 rounded flex items-center justify-center text-white mr-3">
                            <span class="font-bold text-sm">VISA</span>
                        </div>
                        @elseif($paymentMethod->card_brand === 'mastercard')
                        <div class="w-12 h-8 bg-red-600 rounded flex items-center justify-center text-white mr-3">
                            <span class="font-bold text-sm">MC</span>
                        </div>
                        @else
                        <div class="w-12 h-8 bg-gray-600 rounded flex items-center justify-center text-white mr-3">
                            <span class="font-bold text-sm">CARD</span>
                        </div>
                        @endif
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Card ending in</p>
                            <p class="font-medium text-gray-900 dark:text-white">•••• {{ $paymentMethod->last_four }}</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name on Card <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" value="{{ old('name', $paymentMethod->name) }}" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Card Type <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="border rounded-md p-3 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors" onclick="document.getElementById('card_brand_visa').checked = true">
                                <div class="flex items-center">
                                    <input type="radio" id="card_brand_visa" name="card_brand" value="visa" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600" {{ old('card_brand', $paymentMethod->card_brand) === 'visa' ? 'checked' : '' }} required>
                                    <label for="card_brand_visa" class="ml-3 flex items-center cursor-pointer">
                                        <div class="w-12 h-8 bg-blue-600 rounded flex items-center justify-center text-white mr-2">
                                            <span class="font-bold text-sm">VISA</span>
                                        </div>
                                        <span class="font-medium">Visa</span>
                                    </label>
                                </div>
                            </div>
                            <div class="border rounded-md p-3 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors" onclick="document.getElementById('card_brand_mastercard').checked = true">
                                <div class="flex items-center">
                                    <input type="radio" id="card_brand_mastercard" name="card_brand" value="mastercard" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600" {{ old('card_brand', $paymentMethod->card_brand) === 'mastercard' ? 'checked' : '' }}>
                                    <label for="card_brand_mastercard" class="ml-3 flex items-center cursor-pointer">
                                        <div class="w-12 h-8 bg-red-600 rounded flex items-center justify-center text-white mr-2">
                                            <span class="font-bold text-sm">MC</span>
                                        </div>
                                        <span class="font-medium">Mastercard</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="expiry_month" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Expiry Month <span class="text-red-500">*</span></label>
                            <select id="expiry_month" name="expiry_month" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md" required>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ sprintf('%02d', $i) }}" {{ old('expiry_month', $paymentMethod->expiry_month) == sprintf('%02d', $i) ? 'selected' : '' }}>{{ sprintf('%02d', $i) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label for="expiry_year" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Expiry Year <span class="text-red-500">*</span></label>
                            <select id="expiry_year" name="expiry_year" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md" required>
                                @for($i = date('Y'); $i <= date('Y') + 10; $i++)
                                    <option value="{{ $i }}" {{ old('expiry_year', $paymentMethod->expiry_year) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
                @elseif($paymentMethod->payment_type === 'paypal')
                <div class="mb-4">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-8 bg-blue-500 rounded flex items-center justify-center text-white mr-3">
                            <span class="font-bold text-sm">PP</span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">PayPal Account</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $paymentMethod->billing_email }}</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Account Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" value="{{ old('name', $paymentMethod->name) }}" required>
                    </div>

                    <div class="mb-4">
                        <label for="billing_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">PayPal Email <span class="text-red-500">*</span></label>
                        <input type="email" name="billing_email" id="billing_email" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" value="{{ old('billing_email', $paymentMethod->billing_email) }}" required>
                    </div>
                </div>
                @endif

                <div class="mb-4">
                    <div class="flex items-center">
                        <input id="is_default" name="is_default" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded" {{ old('is_default', $paymentMethod->is_default) ? 'checked' : '' }} value="1">
                        <label for="is_default" class="ml-2 block text-sm text-gray-900 dark:text-white">
                            Set as default payment method
                        </label>
                    </div>
                </div>

                <div class="flex justify-end">
                    <a href="{{ route('vendor.payment.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Update Payment Method
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');

        form.addEventListener('submit', function(e) {
            // Don't prevent default form submission
            // e.preventDefault();

            // Validate form
            const paymentType = form.querySelector('input[name="payment_type"]').value;
            const nameInput = form.querySelector('#name');

            if (!nameInput.value.trim()) {
                e.preventDefault();
                alert('Please enter a name for this payment method.');
                nameInput.focus();
                return;
            }

            if (paymentType === 'credit_card') {
                // Check if card type is selected
                const cardType = form.querySelector('input[name="card_brand"]:checked');
                if (!cardType) {
                    e.preventDefault();
                    alert('Please select a card type.');
                    return;
                }
            } else if (paymentType === 'paypal') {
                // Check PayPal email
                const billingEmail = form.querySelector('#billing_email');
                if (!billingEmail.value.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(billingEmail.value.trim())) {
                    e.preventDefault();
                    alert('Please enter a valid PayPal email address.');
                    billingEmail.focus();
                    return;
                }
            }

            // Let the form submit naturally
            console.log('Form is valid, submitting with payment_type:', paymentType);
        });
    });
</script>
@endsection
