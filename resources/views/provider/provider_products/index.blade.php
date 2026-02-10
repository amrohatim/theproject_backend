@extends('layouts.dashboard')

@section('title', __('provider.product_inventory'))
@section('page-title', __('provider.product_inventory'))

@section('styles')
<style>
    @media (max-width: 768px) {
        .responsive-table thead {
            display: none;
        }

        .responsive-table,
        .responsive-table tbody,
        .responsive-table tr,
        .responsive-table td {
            display: block;
            width: 100%;
        }

        .responsive-table tbody tr {
            margin-bottom: 1rem;
            border: 1px solid #3b82f6 !important;
            border-radius: 0.375rem !important;
            overflow: hidden;
            background-color: #ffffff;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.06);
        }

        .dark .responsive-table tbody tr {
            background-color: #1f2937;
            border-color: #60a5fa !important;
        }

        .responsive-table td {
            position: relative;
            padding: 0.75rem 1rem 0.75rem 9.5rem;
            text-align: left;
            white-space: normal;
        }

        .responsive-table td::before {
            content: attr(data-label);
            position: absolute;
            left: 1rem;
            top: 0.75rem;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #6b7280;
        }

        .dark .responsive-table td::before {
            color: #9ca3af;
        }

        .responsive-table td:last-child {
            text-align: left;
        }
    }
</style>
@endsection

@section('content')
<div class="container mx-auto">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('provider.product_inventory') }}</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">{{ __('provider.manage_store_products') }}</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('provider.provider-products.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i> {{ __('provider.add_product') }}
            </a>
        </div>
    </div>

    <!-- Search and filters (UI only) -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 p-4 border border-gray-200 dark:border-gray-700">
        <form action="{{ route('provider.provider-products.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('provider.search_products') }}</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" placeholder="{{ __('provider.search_products') }}">
                    </div>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('provider.status') }}</label>
                    <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="">{{ __('messages.all_status') ?? 'All Status' }}</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>{{ __('provider.active') }}</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>{{ __('provider.inactive') }}</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-filter mr-2"></i> {{ __('provider.filter') ?? __('messages.filter') }}
                </button>
            </div>
        </form>
    </div>

    <!-- Products list -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 border border-gray-200 dark:border-gray-700">
        <div class="hidden md:block overflow-x-auto">
            <table class="responsive-table min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('provider.product') ?? __('messages.product') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('provider.price') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('provider.stock') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('provider.status') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('provider.added_date') }}</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('provider.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($providerProducts as $item)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap" data-label="{{ __('provider.product') ?? __('messages.product') }}">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @php
                                        $imageUrl = \App\Helpers\ProviderImageHelper::getProviderProductImageUrl($item->image ?? null);
                                        $placeholderUrl = asset('images/placeholder.jpg');
                                    @endphp
                                    <img
                                        src="{{ $imageUrl }}"
                                        alt="{{ $item->product_name }}"
                                        class="h-10 w-10 rounded-md object-cover"
                                        onerror="this.onerror=null; this.src='{{ $placeholderUrl }}'; if (!this.src) this.parentElement.innerHTML='<div class=\'h-10 w-10 rounded-md bg-gray-200 dark:bg-gray-700 flex items-center justify-center\'><i class=\'fas fa-image text-gray-400\'></i></div>';"
                                    >
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->product_name }}</div>
                                    @if(isset($item->description) && $item->description)
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ \Illuminate\Support\Str::limit($item->description, 50) }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap" data-label="{{ __('provider.price') }}">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">${{ number_format($item->price, 2) }}</div>
                            @if(!empty($item->original_price))
                                <div class="text-xs text-gray-500 dark:text-gray-400 line-through">${{ number_format($item->original_price, 2) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap" data-label="{{ __('provider.stock') }}">
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $item->stock }} {{ __('provider.units') ?? 'units' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap" data-label="{{ __('provider.status') }}">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($item->is_active) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                                {{ $item->is_active ? __('provider.active') : __('provider.inactive') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap" data-label="{{ __('provider.added_date') }}">
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $item->created_at->format('M d, Y') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium" data-label="{{ __('provider.actions') }}">
                            <a href="{{ route('provider.provider-products.edit', $item->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 mr-3" title="{{ __('provider.edit_product') }}">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('provider.provider-products.destroy', $item->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300" onclick="return confirm('{{ __('provider.delete_product_message') }}');" title="{{ __('provider.delete_product') }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center py-4">
                                <i class="fas fa-shopping-bag text-gray-300 dark:text-gray-600 text-5xl mb-4"></i>
                                <p>{{ __('provider.no_products_inventory') }}</p>
                                <a href="{{ route('provider.provider-products.create') }}" class="mt-2 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    <i class="fas fa-plus mr-2"></i> {{ __('provider.add_first_product') }}
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="block md:hidden space-y-4 px-4 py-4">
            @forelse($providerProducts as $item)
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm bg-white dark:bg-gray-800">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-12 w-12">
                            @php
                                $imageUrl = \App\Helpers\ProviderImageHelper::getProviderProductImageUrl($item->image ?? null);
                                $placeholderUrl = asset('images/placeholder.jpg');
                            @endphp
                            <img
                                src="{{ $imageUrl }}"
                                alt="{{ $item->product_name }}"
                                class="h-12 w-12 rounded-md object-cover"
                                onerror="this.onerror=null; this.src='{{ $placeholderUrl }}';"
                            >
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $item->product_name }}</div>
                            @if(isset($item->description) && $item->description)
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ \Illuminate\Support\Str::limit($item->description, 40) }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-3 text-sm text-gray-600 dark:text-gray-400">
                        <div>
                            <div class="text-xs uppercase tracking-wider text-gray-400 dark:text-gray-500">{{ __('provider.price') }}</div>
                            <div class="font-medium text-gray-900 dark:text-white">${{ number_format($item->price, 2) }}</div>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-wider text-gray-400 dark:text-gray-500">{{ __('provider.stock') }}</div>
                            <div class="text-gray-900 dark:text-white">{{ $item->stock }} {{ __('provider.units') ?? 'units' }}</div>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-wider text-gray-400 dark:text-gray-500">{{ __('provider.status') }}</div>
                            <span class="inline-flex mt-1 px-2 text-xs leading-5 font-semibold rounded-full
                                @if($item->is_active) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                                {{ $item->is_active ? __('provider.active') : __('provider.inactive') }}
                            </span>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-wider text-gray-400 dark:text-gray-500">{{ __('provider.added_date') }}</div>
                            <div class="text-gray-900 dark:text-white">{{ $item->created_at->format('M d, Y') }}</div>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end space-x-3">
                        <a href="{{ route('provider.provider-products.edit', $item->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300" title="{{ __('provider.edit_product') }}">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('provider.provider-products.destroy', $item->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300" onclick="return confirm('{{ __('provider.delete_product_message') }}');" title="{{ __('provider.delete_product') }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 px-4">
                    <i class="fas fa-shopping-bag text-gray-300 dark:text-gray-600 text-5xl mb-4"></i>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('provider.no_products_inventory') }}</p>
                    <a href="{{ route('provider.provider-products.create') }}" class="mt-3 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <i class="fas fa-plus mr-2"></i> {{ __('provider.add_first_product') }}
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $providerProducts->withQueryString()->links() }}
    </div>
</div>
@endsection
