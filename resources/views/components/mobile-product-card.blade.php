{{-- Mobile product card component --}}
@php
    $editUrl = $editUrl ?? null;
    $deleteUrl = $deleteUrl ?? null;
    $deleteConfirm = $deleteConfirm ?? __('merchant.delete_product_confirm');
    $editButtonClass = $editButtonClass ?? 'bg-blue-500 hover:bg-blue-600';
@endphp

<div class="bg-white border border-gray-200 rounded-[8px] shadow-sm">
    <div class="px-6 pt-6">
        <div class="flex items-start justify-between">
            <div class="flex items-start gap-6">
                <div class="flex flex-col items-center gap-3">
                    <div class="h-[106px] w-[142px] overflow-hidden rounded-lg bg-gray-100 border border-gray-200">
                        @if($product->image)
                            <img class="h-full w-full object-cover" src="{{ asset($product->image) }}" alt="{{ $product->name }}">
                        @else
                            <div class="flex h-full w-full items-center justify-center">
                                <i class="fas fa-image text-gray-400 text-lg"></i>
                            </div>
                        @endif
                    </div>
                    @if(isset($product->home_service))
                        <div class="text-[11px] font-semibold tracking-tight text-blue-500">
                            {{ $product->home_service ? __('merchant.home_service') : __('merchant.in_store') }}
                        </div>
                    @elseif($product->stock !== null)
                        <div class="text-[11px] font-semibold tracking-tight text-blue-500">
                            {{ $product->stock }} {{ __('merchant.available_stock') }}
                        </div>
                    @else
                        <div class="text-[11px] font-semibold tracking-tight text-gray-400">
                            {{ __('merchant.not_tracked') }}
                        </div>
                    @endif
                </div>
                    <div class="flex flex-col gap-3">
                        <div class="flex flex-col gap-1">
                            <div class="text-lg font-semibold text-gray-900">{{ $product->name }}</div>
                            <div class="text-sm font-semibold text-gray-900">${{ number_format($product->price, 2) }}</div>
                            <div class="text-sm font-medium text-gray-700">{{ $product->category->name ?? __('merchant.uncategorized') }}</div>
                        @if(!empty($product->description))
                            <div class="text-xs text-gray-400">{{ \Illuminate\Support\Str::limit($product->description, 36) }}</div>
                        @endif
                        @if(!empty($product->sku))
                            <div class="text-xs text-gray-400">{{ \Illuminate\Support\Str::limit($product->sku, 36) }}</div>
                        @endif
                    </div>
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-2 text-[11px] font-medium {{ $product->is_available ? 'text-emerald-500' : 'text-gray-400' }}">
                            <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-7.5 7.5a1 1 0 01-1.414 0l-3.5-3.5a1 1 0 011.414-1.414l2.793 2.793 6.793-6.793a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            {{ $product->is_available ? __('merchant.available') : __('merchant.unavailable') }}
                        </div>
                        <div class="text-[11px] text-gray-400">
                            {{ __('merchant.date_created') }}
                        </div>
                        <div class="text-sm font-semibold text-gray-400">
                            {{ $product->created_at->format('M d, Y') }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="pt-1">
                <input type="checkbox" class="product-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4" value="{{ $product->id }}">
            </div>
        </div>
    </div>
    <div class="mt-6 border-t border-gray-200"></div>
    <div class="px-6 py-4">
        <div class="flex items-center gap-4">
            <a href="{{ $editUrl ?? route('merchant.products.edit', $product->id) }}"
               class="inline-flex flex-1 items-center justify-center rounded-lg px-6 py-3 text-sm font-semibold text-white transition-colors {{ $editButtonClass }}">
                {{ __('merchant.edit') }}
            </a>
            <div class="h-6 w-px bg-gray-300"></div>
            <form action="{{ $deleteUrl ?? route('merchant.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('{{ $deleteConfirm }}');">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex flex-1 items-center justify-center rounded-lg bg-red-500 px-6 py-3 text-sm font-semibold text-white hover:bg-red-600 transition-colors">
                    {{ __('merchant.delete') }}
                </button>
            </form>
        </div>
    </div>
</div>
