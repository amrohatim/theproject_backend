@extends('layouts.dashboard')

@section('title', __('messages.branch_details'))
@section('page-title', __('messages.branch_details'))

@section('content')
<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $branch->name }}</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">{{ __('messages.branch_details_and_information') }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('vendor.branches.edit', $branch->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-edit mr-2"></i> {{ __('messages.edit') }}
            </a>
            <a href="{{ route('vendor.branches.image', $branch->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-image mr-2"></i> {{ __('messages.manage_image') }}
            </a>
            <a href="{{ route('vendor.branches.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-arrow-left mr-2"></i> {{ __('messages.back_to_branches') }}
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden border border-gray-200 dark:border-gray-700 mb-6">
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('messages.branch_information') }}</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="col-span-1 flex flex-col items-center">
                    <div class="w-40 h-40 bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-600 mb-3">
                        @php
                            // Use the branch model's accessor method which handles all the fallback logic
                            $branchImage = $branch->getBranchImageAttribute();

                            // Determine image source for display purposes
                            $imageSource = 'placeholder';
                            if (!$branch->use_company_image && $branch->branch_image) {
                                $imageSource = 'branch';
                            } elseif ($branch->use_company_image && $branch->company && $branch->company->logo) {
                                $imageSource = 'company';
                            } elseif ($branch->image) {
                                $imageSource = 'legacy';
                            }
                        @endphp

                        @if($branchImage)
                            <img src="{{ $branchImage }}" alt="{{ $branch->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gray-200 dark:bg-gray-600">
                                <i class="fas fa-store text-gray-400 dark:text-gray-500 text-4xl"></i>
                            </div>
                        @endif
                    </div>
                    <div class="text-center">
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            @if($imageSource == 'branch')
                                {{ __('messages.using_branch_image') }}
                            @elseif($imageSource == 'company')
                                {{ __('messages.using_company_image') }}
                            @elseif($imageSource == 'legacy')
                                {{ __('messages.using_default_image') }}
                            @else
                                {{ __('messages.no_image_available') }}
                            @endif
                        </span>
                    </div>
                </div>
                <div class="col-span-1 md:col-span-2">
                <div>
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.branch_name') }}</h4>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $branch->name }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.company') }}</h4>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $branch->company->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.email_address') }}</h4>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                        @if($branch->email)
                            <a href="mailto:{{ $branch->email }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                {{ $branch->email }}
                            </a>
                        @else
                            N/A
                        @endif
                    </p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.phone_number') }}</h4>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                        @if($branch->phone)
                            <a href="tel:{{ $branch->phone }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                {{ $branch->phone }}
                            </a>
                        @else
                            N/A
                        @endif
                    </p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.address') }}</h4>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $branch->address ?? 'N/A' }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.status') }}</h4>
                    <p class="mt-1">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $branch->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                            {{ ucfirst($branch->status) }}
                        </span>
                    </p>
                </div>
                <div class="col-span-1 md:col-span-2">
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.description') }}</h4>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $branch->description ?? __('messages.no_description_available') }}</p>
                </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Business Hours Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden border border-gray-200 dark:border-gray-700 mb-6">
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('messages.business_hours') }}</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @php
                    $openingHours = $branch->opening_hours ?? [];
                    $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                @endphp

                @foreach($days as $day)
                    @php
                        $dayData = $openingHours[$day] ?? ['is_open' => false, 'open' => null, 'close' => null];
                        $isOpen = $dayData['is_open'] ?? false;
                        $openTime = $dayData['open'] ?? '09:00';
                        $closeTime = $dayData['close'] ?? '17:00';
                    @endphp
                    <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-md">
                        <div>
                            <span class="font-medium text-gray-700 dark:text-gray-300 capitalize">{{ __('messages.' . $day) }}</span>
                        </div>
                        <div>
                            @if($isOpen)
                                <span class="text-green-600 dark:text-green-400">
                                    {{ date('g:i A', strtotime($openTime)) }} - {{ date('g:i A', strtotime($closeTime)) }}
                                </span>
                            @else
                                <span class="text-red-600 dark:text-red-400">{{ __('messages.closed') }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Products Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden border border-gray-200 dark:border-gray-700 mb-6">
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('messages.products') }}</h3>
            <a href="{{ route('vendor.products.create') }}" class="inline-flex items-center px-3 py-1 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-plus mr-1"></i> {{ __('messages.add_product') }}
            </a>
        </div>
        <div class="p-6">
            @if(count($branch->products ?? []) > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($branch->products as $product)
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg overflow-hidden shadow-sm border border-gray-200 dark:border-gray-600">
                            <div class="h-40 bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                @php
                                    // Directly check if image exists in the public directory
                                    $imagePath = 'images/products/' . basename($product->image ?? '');
                                    $fileExists = file_exists(public_path($imagePath)) && $product->image;
                                @endphp
                                @if($fileExists)
                                    <img src="/{{ $imagePath }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                                @else
                                    <i class="fas fa-box text-gray-400 dark:text-gray-500 text-4xl"></i>
                                @endif
                            </div>
                            <div class="p-4">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ $product->name }}</h4>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">${{ number_format($product->price, 2) }}</p>
                                <div class="mt-2 flex justify-end">
                                    <a href="{{ route('vendor.products.edit', $product->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 text-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6">
                    <p class="text-gray-500 dark:text-gray-400">{{ __('messages.no_products_found_for_this_branch') }}</p>
                    <a href="{{ route('vendor.products.create') }}" class="mt-2 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-plus mr-2"></i> {{ __('messages.add_your_first_product') }}
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Services Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('messages.services') }}</h3>
            <a href="{{ route('vendor.services.create') }}" class="inline-flex items-center px-3 py-1 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-plus mr-1"></i> {{ __('messages.add_service') }}
            </a>
        </div>
        <div class="p-6">
            @if(count($branch->services ?? []) > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($branch->services as $service)
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg overflow-hidden shadow-sm border border-gray-200 dark:border-gray-600">
                            <div class="h-40 bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                @if($service->image)
                                    <img src="{{ $service->image }}" alt="{{ $service->name }}" class="h-full w-full object-cover"
                                         onerror="this.style.display='none'; this.parentElement.innerHTML='<i class=\'fas fa-concierge-bell text-gray-400 dark:text-gray-500 text-4xl\'></i>';">
                                @else
                                    <i class="fas fa-concierge-bell text-gray-400 dark:text-gray-500 text-4xl"></i>
                                @endif
                            </div>
                            <div class="p-4">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ $service->name }}</h4>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">${{ number_format($service->price, 2) }} - {{ $service->duration }} min</p>
                                <div class="mt-2 flex justify-end">
                                    <a href="{{ route('vendor.services.edit', $service->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 text-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6">
                    <p class="text-gray-500 dark:text-gray-400">{{ __('messages.no_services_found_for_this_branch') }}</p>
                    <a href="{{ route('vendor.services.create') }}" class="mt-2 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-plus mr-2"></i> {{ __('messages.add_your_first_service') }}
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
