@extends('layouts.provider')

@section('title', __('provider.product_inventory'))

@section('header', __('provider.products_inventory'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
        {{-- <div style="width: 40px; height: 40px; background-color: var(--discord-primary); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
            <i class="fas fa-box-open text-white"></i>
        </div> --}}
        <div>
            <h4 class="mb-0">{{ __('provider.product_inventory') }}</h4>
            <p class="text-muted mb-0" style="font-size: 14px; color: var(--discord-light);">{{ __('provider.manage_store_products') }}</p>
        </div>
    </div>
    <a href="{{ route('provider.provider-products.create') }}" class="discord-btn">
        <i class="fas fa-plus me-2"></i> {{ __('provider.add_product') }}
    </a>
</div>

<!-- Main Content -->
<div class="discord-card mb-4">
    <div class="discord-card-header d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-box me-2" style="color: var(--discord-primary);"></i>
             
            {{ __('provider.products') }} ({{ $providerProducts->total() }})
        </div>
      
        <div class="d-flex">
            <input type="text" class="discord-input me-2" placeholder="{{ __('provider.search_products') }}" id="product-search" style="width: 200px; margin-bottom: 0; height: 36px;">
            <div class="dropdown">
                <button class=" dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                   {{ __('provider.actions') }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end" style="background-color: var(--discord-darker); border: 1px solid var(--discord-darkest);" aria-labelledby="dropdownMenuButton">
                    <li><a class="dropdown-item" href="{{ route('provider.provider-products.create') }}" style="color: var(--discord-lightest);"><i class="fas fa-plus me-2"></i> {{ __('provider.add_new') }}</a></li>
                    <li><a class="dropdown-item" href="#" style="color: var(--discord-lightest);"><i class="fas fa-file-export me-2"></i> {{ __('provider.export_csv') }}</a></li>
                    <li><a class="dropdown-item" href="#" style="color: var(--discord-lightest);"><i class="fas fa-sort-amount-down me-2"></i> {{ __('provider.sort_by') }} {{ __('provider.price') }}</a></li>
                </ul>
            </div>
        </div>
    </div>

    @if($providerProducts->count() > 0)
        <div class="table-responsive">
            <table class="discord-table" id="products-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">{{ __('provider.image') }}</th>
                        <th>{{ __('provider.name') }}</th>
                        <th>{{ __('provider.price') }}</th>
                        <th>{{ __('provider.stock') }}</th>
                        <th>{{ __('provider.status') }}</th>
                        <th>{{ __('provider.added_date') }}</th>
                        <th style="width: 120px;">{{ __('provider.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($providerProducts as $item)
                        <tr>
                            <td>
                                @if($item->image)
                                    <img src="@providerProductImage($item->image)" alt="{{ $item->product_name }}" width="40" height="40" class="rounded" style="object-fit: cover;">
                                @else
                                    <div style="width: 40px; height: 40px; background-color: var(--discord-darkest); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-box" style="color: var(--discord-light);"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div style="font-weight: 500; color: var(--discord-lightest);">{{ $item->product_name }}</div>
                                @if(isset($item->description) && $item->description)
                                    <div style="font-size: 12px; color: var(--discord-light);">{{ Str::limit($item->description, 50) }}</div>
                                @endif
                            </td>
                            <td style="color: var(--discord-green); font-weight: 500;">${{ number_format($item->price, 2) }}</td>
                            <td>
                                <span style="background-color: var(--discord-darkest); color: var(--discord-lightest); padding: 2px 8px; border-radius: 10px; font-size: 12px; display: inline-block;">
                                    {{ $item->stock }} units
                                </span>
                            </td>
                            <td>
                                @if($item->is_active)
                                    <span style="background-color: var(--discord-green); color: white; padding: 2px 8px; border-radius: 10px; font-size: 12px; display: inline-block;">
                                        {{ __('provider.active') }}
                                    </span>
                                @else
                                    <span style="background-color: var(--discord-darkest); color: var(--discord-light); padding: 2px 8px; border-radius: 10px; font-size: 12px; display: inline-block;">
                                        {{ __('provider.inactive') }}
                                    </span>
                                @endif
                            </td>
                            <td>{{ $item->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('provider.provider-products.edit', $item->id) }}" class="btn btn-sm" style="background-color: var(--discord-primary); color: white; border-radius: 4px;" title="{{ __('provider.edit_product') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('provider.provider-products.destroy', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm" style="background-color: var(--discord-red); color: white; border-radius: 4px;" onclick="return confirm('{{ __('provider.delete_product_message') }}');" title="{{ __('provider.delete_product') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination with Discord Styling -->
        <div class="p-3 d-flex justify-content-between align-items-center">
            <div style="color: var(--discord-light); font-size: 14px;">
                {{ __('provider.showing') }} {{ $providerProducts->firstItem() ?? 0 }} {{ __('provider.to') }} {{ $providerProducts->lastItem() ?? 0 }} {{ __('provider.of') }} {{ $providerProducts->total() }} {{ __('provider.products') }}
            </div>
            <div class="discord-pagination">
                {{ $providerProducts->links() }}
            </div>
        </div>
    @else
        <div class="text-center py-5" style="color: var(--discord-light);">
            <div style="width: 80px; height: 80px; background-color: var(--discord-darkest); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                <i class="fas fa-box-open fa-2x"></i>
            </div>
            <h4>{{ __('provider.no_products_inventory') }}</h4>
            <p class="mb-4">{{ __('provider.start_adding_products') }}</p>
            <a href="{{ route('provider.provider-products.create') }}" class="discord-btn">
                <i class="fas fa-plus me-2"></i> {{ __('provider.add_first_product') }}
            </a>
        </div>
    @endif
</div>

<!-- Quick Stats -->
<div class="row mb-4">
    <div class="col-lg-4 mb-4">
        <div class="discord-card h-100">
            <div class="discord-card-header">
                <i class="fas fa-chart-pie me-2" style="color: var(--discord-primary);"></i>
                {{ __('provider.inventory_stats') }}
            </div>
            <div class="p-3">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="width: 36px; height: 36px; background-color: var(--discord-darkest); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                        <i class="fas fa-boxes" style="color: var(--discord-primary);"></i>
                    </div>
                    <div style="flex-grow: 1;">
                        <div style="color: var(--discord-light); font-size: 12px;">{{ __('provider.total_products') }}</div>
                        <div style="font-weight: 600; font-size: 18px;">{{ $providerProducts->total() }}</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="width: 36px; height: 36px; background-color: var(--discord-darkest); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                        <i class="fas fa-tag" style="color: var(--discord-green);"></i>
                    </div>
                    <div style="flex-grow: 1;">
                        <div style="color: var(--discord-light); font-size: 12px;">{{ __('provider.active_products') }}</div>
                        <div style="font-weight: 600; font-size: 18px;">{{ $providerProducts->where('is_active', true)->count() }}</div>
                    </div>
                </div>
                <div class="d-flex gap-3 align-items-center">
                    <div style="width: 36px; height: 36px; background-color: var(--discord-darkest); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                        <i class="fas fa-warehouse" style="color: var(--discord-yellow);"></i>
                    </div>
                    <div style="flex-grow: 1;">
                        <div style="color: var(--discord-light); font-size: 12px;">{{ __('provider.out_of_stock') }}</div>
                        <div style="font-weight: 600; font-size: 18px;">{{ $providerProducts->where('stock', 0)->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8 mb-4">
        <div class="discord-card h-100">
            <div class="discord-card-header">
                <i class="fas fa-tasks me-2" style="color: var(--discord-primary);"></i>
                {{ __('provider.quick_actions') }}
            </div>
            <div class="p-3">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div style="background-color: var(--discord-darkest); border-radius: 8px; padding: 20px; text-align: center; height: 100%;">
                            <i class="fas fa-plus mb-3" style="color: var(--discord-primary); font-size: 24px;"></i>
                            <div style="font-weight: 600; margin-bottom: 10px;">{{ __('provider.add_product') }}</div>
                            <a href="{{ route('provider.provider-products.create') }}" class="discord-btn btn-sm w-100">
                                {{ __('provider.go') }}
                            </a>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div style="background-color: var(--discord-darkest); border-radius: 8px; padding: 20px; text-align: center; height: 100%;">
                            <i class="fas fa-sync-alt mb-3" style="color: var(--discord-green); font-size: 24px;"></i>
                            <div style="font-weight: 600; margin-bottom: 10px;">{{ __('provider.update_stock') }}</div>
                            <a href="#" class="discord-btn btn-sm w-100" style="background-color: var(--discord-green);">
                                {{ __('provider.go') }}
                            </a>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div style="background-color: var(--discord-darkest); border-radius: 8px; padding: 20px; text-align: center; height: 100%;">
                            <i class="fas fa-file-export mb-3" style="color: var(--discord-yellow); font-size: 24px;"></i>
                            <div style="font-weight: 600; margin-bottom: 10px;">{{ __('provider.export_csv') }}</div>
                            <a href="#" class="discord-btn btn-sm w-100" style="background-color: var(--discord-yellow);">
                                {{ __('provider.go') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable if it exists
        if ($.fn.dataTable && $('#dataTable').length) {
            $('#dataTable').DataTable({
                "paging": false,
                "searching": true,
                "ordering": true,
                "info": false,
            });
        }
    });
</script>
@endsection
