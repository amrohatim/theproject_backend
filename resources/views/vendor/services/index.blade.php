@extends('layouts.dashboard')

@section('title', 'Services')
@section('page-title', 'Services')

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
            border: 1px solid #A46BC1 !important;
            border-radius: 0.375rem !important;
            overflow: hidden;
            background-color: #ffffff;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.06);
        }

        .dark .responsive-table tbody tr {
            background-color: #1f2937;
            border-color: #A46BC1 !important;
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
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('messages.services') }}</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">{{ __('messages.manage_your_services') }}</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('vendor.services.select-branch') }}" class="inline-flex items-center px-4 py-2 bg-[var(--primary)] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[var(--primary)] active:bg-[var(--primary)] focus:outline-none focus:border-[var(--primary)] focus:ring ring-[var(--primary)] disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i> {{ __('messages.add_service') }}
            </a>
        </div>
    </div>

    <!-- Search and filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 p-4 border border-gray-200 dark:border-gray-700">
        <form action="{{ route('vendor.services.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.search') }}</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" class="focus:ring-[var(--primary)] focus:border-[var(--primary)] block w-full px-2 sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" placeholder="{{ __('messages.search_services') }}">
                    </div>
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.category') }}</label>
                    <select id="category" name="category" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-[var(--primary)] focus:border-[var(--primary)] sm:text-sm rounded-md">
                        <option value="">{{ __('messages.all_categories') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @if($category->children->count() > 0)
                                @foreach($category->children as $child)
                                    <option value="{{ $child->id }}" {{ request('category') == $child->id ? 'selected' : '' }}>
                                        &nbsp;&nbsp;&nbsp;&nbsp;{{ $child->name }}
                                    </option>
                                @endforeach
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-[var(--primary)] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[var(--primary)] active:bg-[var(--primary)] focus:outline-none focus:border-[var(--primary)] focus:ring ring-[var(--primary)] disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-filter mr-2"></i> {{ __('messages.filter') }}
                </button>
            </div>
        </form>
    </div>

    @php
        $tabBaseQuery = request()->except(['branch', 'page']);
        $activeBranch = (string) request('branch', '');
    @endphp

    <div class="mb-6">
        <div class="overflow-x-auto">
            <nav class="inline-flex min-w-full whitespace-nowrap border-b border-gray-200 dark:border-gray-700">
                <a
                    href="{{ route('vendor.services.index', $tabBaseQuery) }}"
                    class="border-b-2 px-4 py-3 text-sm font-medium transition-colors {{ $activeBranch === '' ? 'border-[var(--primary)] text-[var(--primary)] dark:border-[var(--primary)] dark:text-[var(--primary)]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
                >
                    {{ __('messages.services') }}
                </a>

                @foreach($branches as $branch)
                    <a
                        href="{{ route('vendor.services.index', array_merge($tabBaseQuery, ['branch' => $branch->id])) }}"
                        class="border-b-2 px-4 py-3 text-sm font-medium transition-colors {{ $activeBranch === (string) $branch->id ? 'border-[var(--primary)] text-[var(--primary)] dark:border-[var(--primary)] dark:text-[var(--primary)]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
                    >
                        {{ $branch->name }}
                    </a>
                @endforeach
            </nav>
        </div>
    </div>

    <!-- Services list -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 border border-gray-200 dark:border-gray-700">
        <div class="block md:hidden space-y-4 px-4 py-4">
            @forelse($services ?? [] as $service)
                <x-mobile-product-card
                    :product="$service"
                    :edit-url="route('vendor.services.edit', $service->id)"
                    :delete-url="route('vendor.services.destroy', $service->id)"
                    :delete-confirm="__('messages.delete_service_confirmation')"
                />
            @empty
                <div class="text-center py-12 px-4">
                    <i class="fas fa-concierge-bell text-gray-300 dark:text-gray-600 text-5xl mb-4"></i>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.no_services_found') }}</p>
                    <a href="{{ route('vendor.services.select-branch') }}" class="mt-3 inline-flex items-center px-4 py-2 bg-[var(--primary)] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[var(--primary)] active:bg-[var(--primary)] focus:outline-none focus:border-[var(--primary)] focus:ring ring-[var(--primary)] disabled:opacity-25 transition ease-in-out duration-150">
                        <i class="fas fa-plus mr-2"></i> {{ __('messages.add_service') }}
                    </a>
                </div>
            @endforelse
        </div>

        <div class="hidden md:block overflow-x-auto">
            <table class="responsive-table min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.service') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.category') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.branch') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.price') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.duration') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.status') }}</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($services ?? [] as $service)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap" data-label="{{ __('messages.service') }}">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @if($service->image)
                                        <img src="{{ $service->image }}" alt="{{ $service->name }}" class="h-10 w-10 rounded-md object-cover"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="h-10 w-10 rounded-md bg-gray-200 dark:bg-gray-700 flex items-center justify-center" style="display: none;">
                                            <i class="fas fa-image text-gray-400"></i>
                                        </div>
                                    @else
                                        <div class="h-10 w-10 rounded-md bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                            <i class="fas fa-image text-gray-400"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $service->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap" data-label="{{ __('messages.category') }}">
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $service->category->name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap" data-label="{{ __('messages.branch') }}">
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $service->branch->name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap" data-label="{{ __('messages.price') }}">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($service->price, 2) }} {{__('messages.aed')}}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap" data-label="{{ __('messages.duration') }}">
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $service->duration }} {{ __('messages.min') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap" data-label="{{ __('messages.status') }}">
                            @php
                                $serviceStatus = $service->status ?? 'pending';
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($serviceStatus === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @elseif($serviceStatus === 'rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 @endif">
                                {{ ucfirst($serviceStatus) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium" data-label="{{ __('messages.actions') }}">
                            <a href="{{ route('vendor.services.edit', $service->id) }}" class="text-[var(--primary)] dark:text-[var(--primary)] hover:text-[var(--primary)] dark:hover:text-[var(--primary)] mr-3">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('vendor.services.destroy', $service->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300" onclick="return confirm('{{ __('messages.delete_service_confirmation') }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center py-4">
                                <i class="fas fa-concierge-bell text-gray-300 dark:text-gray-600 text-5xl mb-4"></i>
                                <p>{{ __('messages.no_services_found') }}</p>
                                <a href="{{ route('vendor.services.select-branch') }}" class="mt-2 inline-flex items-center px-4 py-2 bg-[var(--primary)] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[var(--primary)] active:bg-[var(--primary)] focus:outline-none focus:border-[var(--primary)] focus:ring ring-[var(--primary)] disabled:opacity-25 transition ease-in-out duration-150">
                                    <i class="fas fa-plus mr-2"></i> {{ __('messages.add_service') }}
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        @if(method_exists($services, 'links'))
            {{ $services->appends(request()->query())->links() }}
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/vendor-autocomplete.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    if (searchInput) {
        new VendorAutoComplete(searchInput, {
            apiUrl: '{{ route('vendor.services.search-suggestions') }}',
            placeholder: 'Search services, categories, descriptions...'
        });
    }
});
</script>
@endpush
