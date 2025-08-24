@extends('layouts.dashboard')

@section('title', 'Product Specifications')

@section('content')
<div class="container mx-auto">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Product Specifications</h2>
                <p class="mt-1 text-gray-600 dark:text-gray-400">Manage specifications, colors, and sizes for {{ $product->name }}</p>
            </div>
            <div>
                <a href="{{ route('vendor.products.edit', $product->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Product
                </a>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="mb-6">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-8">
                <a href="#specifications" class="tab-link whitespace-nowrap py-4 px-1 border-b-2 border-indigo-500 font-medium text-sm text-indigo-600 dark:text-indigo-400" data-target="specifications-panel">
                    <i class="fas fa-list-ul mr-2"></i> Specifications
                </a>
                <a href="#colors" class="tab-link whitespace-nowrap py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300" data-target="colors-panel">
                    <i class="fas fa-palette mr-2"></i> Colors
                </a>
                <a href="#sizes" class="tab-link whitespace-nowrap py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300" data-target="sizes-panel">
                    <i class="fas fa-ruler-combined mr-2"></i> Sizes
                </a>
                <a href="#branches" class="tab-link whitespace-nowrap py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300" data-target="branches-panel">
                    <i class="fas fa-store mr-2"></i> Branches
                </a>
            </nav>
        </div>
    </div>

    <!-- Specifications Panel -->
    <div id="specifications-panel" class="tab-panel bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
        <form id="specifications-form" action="{{ route('vendor.products.specifications.update', $product->id) }}" method="POST">
            @csrf
            <div class="mb-4 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Product Specifications</h3>
                <button type="button" id="add-specification" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-plus mr-2"></i> Add Specification
                </button>
            </div>

            <div id="specifications-container" class="space-y-4">
                @if(count($specifications) > 0)
                    @foreach($specifications as $index => $spec)
                        <div class="specification-item grid grid-cols-12 gap-4 items-center">
                            <div class="col-span-5">
                                <input type="text" name="specifications[{{ $index }}][key]" placeholder="Key (e.g. Material)" value="{{ $spec->key }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                            </div>
                            <div class="col-span-5">
                                <input type="text" name="specifications[{{ $index }}][value]" placeholder="Value (e.g. Cotton)" value="{{ $spec->value }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                            </div>
                            <div class="col-span-1">
                                <input type="number" name="specifications[{{ $index }}][display_order]" placeholder="Order" value="{{ $spec->display_order }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                            </div>
                            <div class="col-span-1 text-center">
                                <button type="button" class="remove-item text-red-500 hover:text-red-700">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="specification-item grid grid-cols-12 gap-4 items-center">
                        <div class="col-span-5">
                            <input type="text" name="specifications[0][key]" placeholder="Key (e.g. Material)" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div>
                        <div class="col-span-5">
                            <input type="text" name="specifications[0][value]" placeholder="Value (e.g. Cotton)" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div>
                        <div class="col-span-1">
                            <input type="number" name="specifications[0][display_order]" placeholder="Order" value="0" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div>
                        <div class="col-span-1 text-center">
                            <button type="button" class="remove-item text-red-500 hover:text-red-700">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                @endif
            </div>

            <div class="mt-6">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-save mr-2"></i> Save Specifications
                </button>
            </div>
        </form>
    </div>

    <!-- Colors Panel -->
    <div id="colors-panel" class="tab-panel bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6 hidden">
        <form id="colors-form" action="{{ route('vendor.products.colors.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Product Colors</h3>
                <button type="button" id="add-color" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-plus mr-2"></i> Add Color
                </button>
            </div>

            <div id="colors-container" class="space-y-6">
                @if(count($colors) > 0)
                    @foreach($colors as $index => $color)
                        <div class="color-item border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <div class="grid grid-cols-12 gap-4">
                                <div class="col-span-3">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Color Name</label>
                                    <select name="colors[{{ $index }}][name]" class="color-name-select focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" required>
                                        <option value="">Select Color</option>
                                        <option value="Red" {{ $color->name == 'Red' ? 'selected' : '' }}>Red</option>
                                        <option value="Crimson" {{ $color->name == 'Crimson' ? 'selected' : '' }}>Crimson</option>
                                        <option value="FireBrick" {{ $color->name == 'FireBrick' ? 'selected' : '' }}>FireBrick</option>
                                        <option value="DarkRed" {{ $color->name == 'DarkRed' ? 'selected' : '' }}>DarkRed</option>
                                        <option value="IndianRed" {{ $color->name == 'IndianRed' ? 'selected' : '' }}>IndianRed</option>
                                        <option value="LightCoral" {{ $color->name == 'LightCoral' ? 'selected' : '' }}>LightCoral</option>
                                        <option value="Salmon" {{ $color->name == 'Salmon' ? 'selected' : '' }}>Salmon</option>
                                        <option value="DarkSalmon" {{ $color->name == 'DarkSalmon' ? 'selected' : '' }}>DarkSalmon</option>
                                        <option value="LightSalmon" {{ $color->name == 'LightSalmon' ? 'selected' : '' }}>LightSalmon</option>
                                        <option value="Orange" {{ $color->name == 'Orange' ? 'selected' : '' }}>Orange</option>
                                        <option value="DarkOrange" {{ $color->name == 'DarkOrange' ? 'selected' : '' }}>DarkOrange</option>
                                        <option value="Coral" {{ $color->name == 'Coral' ? 'selected' : '' }}>Coral</option>
                                        <option value="Tomato" {{ $color->name == 'Tomato' ? 'selected' : '' }}>Tomato</option>
                                        <option value="Gold" {{ $color->name == 'Gold' ? 'selected' : '' }}>Gold</option>
                                        <option value="Yellow" {{ $color->name == 'Yellow' ? 'selected' : '' }}>Yellow</option>
                                        <option value="LightYellow" {{ $color->name == 'LightYellow' ? 'selected' : '' }}>LightYellow</option>
                                        <option value="LemonChiffon" {{ $color->name == 'LemonChiffon' ? 'selected' : '' }}>LemonChiffon</option>
                                        <option value="Khaki" {{ $color->name == 'Khaki' ? 'selected' : '' }}>Khaki</option>
                                        <option value="DarkKhaki" {{ $color->name == 'DarkKhaki' ? 'selected' : '' }}>DarkKhaki</option>
                                        <option value="Green" {{ $color->name == 'Green' ? 'selected' : '' }}>Green</option>
                                        <option value="Lime" {{ $color->name == 'Lime' ? 'selected' : '' }}>Lime</option>
                                        <option value="ForestGreen" {{ $color->name == 'ForestGreen' ? 'selected' : '' }}>ForestGreen</option>
                                        <option value="DarkGreen" {{ $color->name == 'DarkGreen' ? 'selected' : '' }}>DarkGreen</option>
                                        <option value="SeaGreen" {{ $color->name == 'SeaGreen' ? 'selected' : '' }}>SeaGreen</option>
                                        <option value="MediumSeaGreen" {{ $color->name == 'MediumSeaGreen' ? 'selected' : '' }}>MediumSeaGreen</option>
                                        <option value="LightGreen" {{ $color->name == 'LightGreen' ? 'selected' : '' }}>LightGreen</option>
                                        <option value="PaleGreen" {{ $color->name == 'PaleGreen' ? 'selected' : '' }}>PaleGreen</option>
                                        <option value="SpringGreen" {{ $color->name == 'SpringGreen' ? 'selected' : '' }}>SpringGreen</option>
                                        <option value="MediumSpringGreen" {{ $color->name == 'MediumSpringGreen' ? 'selected' : '' }}>MediumSpringGreen</option>
                                        <option value="YellowGreen" {{ $color->name == 'YellowGreen' ? 'selected' : '' }}>YellowGreen</option>
                                        <option value="Olive" {{ $color->name == 'Olive' ? 'selected' : '' }}>Olive</option>
                                        <option value="DarkOliveGreen" {{ $color->name == 'DarkOliveGreen' ? 'selected' : '' }}>DarkOliveGreen</option>
                                        <option value="Blue" {{ $color->name == 'Blue' ? 'selected' : '' }}>Blue</option>
                                        <option value="MediumBlue" {{ $color->name == 'MediumBlue' ? 'selected' : '' }}>MediumBlue</option>
                                        <option value="DarkBlue" {{ $color->name == 'DarkBlue' ? 'selected' : '' }}>DarkBlue</option>
                                        <option value="Navy" {{ $color->name == 'Navy' ? 'selected' : '' }}>Navy</option>
                                        <option value="SkyBlue" {{ $color->name == 'SkyBlue' ? 'selected' : '' }}>SkyBlue</option>
                                        <option value="LightSkyBlue" {{ $color->name == 'LightSkyBlue' ? 'selected' : '' }}>LightSkyBlue</option>
                                        <option value="DeepSkyBlue" {{ $color->name == 'DeepSkyBlue' ? 'selected' : '' }}>DeepSkyBlue</option>
                                        <option value="DodgerBlue" {{ $color->name == 'DodgerBlue' ? 'selected' : '' }}>DodgerBlue</option>
                                        <option value="SteelBlue" {{ $color->name == 'SteelBlue' ? 'selected' : '' }}>SteelBlue</option>
                                        <option value="CornflowerBlue" {{ $color->name == 'CornflowerBlue' ? 'selected' : '' }}>CornflowerBlue</option>
                                        <option value="RoyalBlue" {{ $color->name == 'RoyalBlue' ? 'selected' : '' }}>RoyalBlue</option>
                                        <option value="LightBlue" {{ $color->name == 'LightBlue' ? 'selected' : '' }}>LightBlue</option>
                                        <option value="PowderBlue" {{ $color->name == 'PowderBlue' ? 'selected' : '' }}>PowderBlue</option>
                                        <option value="Purple" {{ $color->name == 'Purple' ? 'selected' : '' }}>Purple</option>
                                        <option value="MediumPurple" {{ $color->name == 'MediumPurple' ? 'selected' : '' }}>MediumPurple</option>
                                        <option value="BlueViolet" {{ $color->name == 'BlueViolet' ? 'selected' : '' }}>BlueViolet</option>
                                        <option value="Violet" {{ $color->name == 'Violet' ? 'selected' : '' }}>Violet</option>
                                        <option value="Orchid" {{ $color->name == 'Orchid' ? 'selected' : '' }}>Orchid</option>
                                        <option value="Magenta" {{ $color->name == 'Magenta' ? 'selected' : '' }}>Magenta</option>
                                        <option value="Fuchsia" {{ $color->name == 'Fuchsia' ? 'selected' : '' }}>Fuchsia</option>
                                        <option value="DeepPink" {{ $color->name == 'DeepPink' ? 'selected' : '' }}>DeepPink</option>
                                        <option value="HotPink" {{ $color->name == 'HotPink' ? 'selected' : '' }}>HotPink</option>
                                        <option value="LightPink" {{ $color->name == 'LightPink' ? 'selected' : '' }}>LightPink</option>
                                        <option value="PaleVioletRed" {{ $color->name == 'PaleVioletRed' ? 'selected' : '' }}>PaleVioletRed</option>
                                        <option value="Brown" {{ $color->name == 'Brown' ? 'selected' : '' }}>Brown</option>
                                        <option value="SaddleBrown" {{ $color->name == 'SaddleBrown' ? 'selected' : '' }}>SaddleBrown</option>
                                        <option value="Sienna" {{ $color->name == 'Sienna' ? 'selected' : '' }}>Sienna</option>
                                        <option value="Chocolate" {{ $color->name == 'Chocolate' ? 'selected' : '' }}>Chocolate</option>
                                        <option value="Peru" {{ $color->name == 'Peru' ? 'selected' : '' }}>Peru</option>
                                        <option value="Tan" {{ $color->name == 'Tan' ? 'selected' : '' }}>Tan</option>
                                        <option value="RosyBrown" {{ $color->name == 'RosyBrown' ? 'selected' : '' }}>RosyBrown</option>
                                        <option value="SandyBrown" {{ $color->name == 'SandyBrown' ? 'selected' : '' }}>SandyBrown</option>
                                        <option value="BurlyWood" {{ $color->name == 'BurlyWood' ? 'selected' : '' }}>BurlyWood</option>
                                        <option value="Wheat" {{ $color->name == 'Wheat' ? 'selected' : '' }}>Wheat</option>
                                        <option value="NavajoWhite" {{ $color->name == 'NavajoWhite' ? 'selected' : '' }}>NavajoWhite</option>
                                        <option value="Black" {{ $color->name == 'Black' ? 'selected' : '' }}>Black</option>
                                        <option value="DimGray" {{ $color->name == 'DimGray' ? 'selected' : '' }}>DimGray</option>
                                        <option value="Gray" {{ $color->name == 'Gray' ? 'selected' : '' }}>Gray</option>
                                        <option value="DarkGray" {{ $color->name == 'DarkGray' ? 'selected' : '' }}>DarkGray</option>
                                        <option value="Silver" {{ $color->name == 'Silver' ? 'selected' : '' }}>Silver</option>
                                        <option value="LightGray" {{ $color->name == 'LightGray' ? 'selected' : '' }}>LightGray</option>
                                        <option value="Gainsboro" {{ $color->name == 'Gainsboro' ? 'selected' : '' }}>Gainsboro</option>
                                        <option value="WhiteSmoke" {{ $color->name == 'WhiteSmoke' ? 'selected' : '' }}>WhiteSmoke</option>
                                        <option value="White" {{ $color->name == 'White' ? 'selected' : '' }}>White</option>
                                    </select>
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Color Code</label>
                                    <input type="text" name="colors[{{ $index }}][color_code]" placeholder="#FF0000" value="{{ $color->color_code }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Price Adjustment</label>
                                    <input type="number" step="0.01" name="colors[{{ $index }}][price_adjustment]" placeholder="0.00" value="{{ $color->price_adjustment }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Stock</label>
                                    <input type="number" name="colors[{{ $index }}][stock]" placeholder="10" value="{{ $color->stock }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Display Order</label>
                                    <input type="number" name="colors[{{ $index }}][display_order]" placeholder="0" value="{{ $color->display_order }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                </div>
                                <div class="col-span-1 flex items-end justify-center">
                                    <button type="button" class="remove-item text-red-500 hover:text-red-700">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mt-4 grid grid-cols-12 gap-4">
                                <div class="col-span-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Color Image</label>
                                    <input type="file" name="color_images[{{ $index }}]" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-gray-700 dark:file:text-gray-200">
                                    <input type="hidden" name="colors[{{ $index }}][image]" value="{{ $color->image }}">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Default Color</label>
                                    <div class="mt-1">
                                        <input type="checkbox" name="colors[{{ $index }}][is_default]" value="1" {{ $color->is_default ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    </div>
                                </div>
                                <div class="col-span-6">
                                    @if($color->image)
                                        <div class="mt-1">
                                            <img src="{{ $color->image }}" alt="{{ $color->name }}" class="h-16 w-16 object-cover rounded-md">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="color-item border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <div class="grid grid-cols-12 gap-4">
                            <div class="col-span-3">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Color Name</label>
                                <input type="text" name="colors[0][name]" placeholder="Red" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" required>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Color Code</label>
                                <input type="text" name="colors[0][color_code]" placeholder="#FF0000" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Price Adjustment</label>
                                <input type="number" step="0.01" name="colors[0][price_adjustment]" placeholder="0.00" value="0" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Stock</label>
                                <input type="number" name="colors[0][stock]" placeholder="10" value="0" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Display Order</label>
                                <input type="number" name="colors[0][display_order]" placeholder="0" value="0" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                            </div>
                            <div class="col-span-1 flex items-end justify-center">
                                <button type="button" class="remove-item text-red-500 hover:text-red-700">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mt-4 grid grid-cols-12 gap-4">
                            <div class="col-span-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Color Image</label>
                                <input type="file" name="color_images[0]" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-gray-700 dark:file:text-gray-200">
                                <input type="hidden" name="colors[0][image]" value="">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Default Color</label>
                                <div class="mt-1">
                                    <input type="checkbox" name="colors[0][is_default]" value="1" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="mt-6">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-save mr-2"></i> Save Colors
                </button>
            </div>
        </form>
    </div>

    <!-- Sizes Panel -->
    <div id="sizes-panel" class="tab-panel bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6 hidden">
        <form action="{{ route('vendor.products.sizes.update', $product->id) }}" method="POST">
            @csrf
            <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-6">
                <h3 class="text-xl font-medium text-gray-900 dark:text-white">Product Sizes</h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Manage size options with category-based selection</p>
            </div>

            <div class="mb-4 flex justify-between items-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">Add size options with price adjustments and stock levels</p>
                <button type="button" id="add-size" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-plus mr-2"></i> Add Size
                </button>
            </div>

            <div id="sizes-container" class="space-y-6">
                @if(count($sizes) > 0)
                    @foreach($sizes as $index => $size)
                        <div class="size-item border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <div class="grid grid-cols-12 gap-4">
                                <div class="col-span-3">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Size Category</label>
                                    <select class="size-category-select focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" required>
                                        <option value="">Select Category</option>
                                        <option value="clothes">Clothes</option>
                                        <option value="shoes">Shoes</option>
                                        <option value="hats">Hats</option>
                                    </select>
                                </div>
                                <div class="size-selection-container col-span-6 grid grid-cols-6 gap-4">
                                    <div class="col-span-3">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Size Name</label>
                                        <input type="text" name="sizes[{{ $index }}][name]" value="{{ $size->name }}" placeholder="Small" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" required>
                                    </div>
                                    <div class="col-span-3">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Size Value</label>
                                        <input type="text" name="sizes[{{ $index }}][value]" value="{{ $size->value }}" placeholder="S" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                    </div>
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Price Adjustment</label>
                                    <input type="number" step="0.01" name="sizes[{{ $index }}][price_adjustment]" value="{{ $size->price_adjustment }}" placeholder="0.00" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                </div>
                                <div class="col-span-1 flex items-end justify-center">
                                    <button type="button" class="remove-item text-red-500 hover:text-red-700">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="grid grid-cols-12 gap-4 mt-4">
                                <div class="col-span-3">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Additional Info</label>
                                    <input type="text" name="sizes[{{ $index }}][additional_info]" value="{{ $size->additional_info ?? '' }}" placeholder="Foot length, age group, etc." class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Stock</label>
                                    <input type="number" name="sizes[{{ $index }}][stock]" value="{{ $size->stock }}" placeholder="10" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Display Order</label>
                                    <input type="number" name="sizes[{{ $index }}][display_order]" value="{{ $size->display_order }}" placeholder="0" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                </div>
                                <div class="col-span-5">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="sizes[{{ $index }}][is_default]" name="sizes[{{ $index }}][is_default]" type="checkbox" class="default-size-checkbox focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded" value="1" {{ $size->is_default ? 'checked' : '' }}>
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="sizes[{{ $index }}][is_default]" class="font-medium text-gray-700 dark:text-gray-300">Default Size</label>
                                            <p class="text-gray-500 dark:text-gray-400">Set as the default size option</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="size-item border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <div class="grid grid-cols-12 gap-4">
                            <div class="col-span-3">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Size Category</label>
                                <select class="size-category-select focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                    <option value="">Manual Entry</option>
                                    <option value="clothes">Clothes</option>
                                    <option value="shoes">Shoes</option>
                                    <option value="hats">Hats</option>
                                </select>
                            </div>
                            <div class="size-selection-container col-span-6 grid grid-cols-6 gap-4">
                                <div class="col-span-3">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Size Name</label>
                                    <input type="text" name="sizes[0][name]" placeholder="Small" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                </div>
                                <div class="col-span-3">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Size Value</label>
                                    <input type="text" name="sizes[0][value]" placeholder="S" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                </div>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Price Adjustment</label>
                                <input type="number" step="0.01" name="sizes[0][price_adjustment]" placeholder="0.00" value="0" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                            </div>
                            <div class="col-span-1 flex items-end justify-center">
                                <button type="button" class="remove-item text-red-500 hover:text-red-700">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="grid grid-cols-12 gap-4 mt-4">
                            <div class="col-span-3">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Additional Info</label>
                                <input type="text" name="sizes[0][additional_info]" placeholder="Foot length, age group, etc." class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Stock</label>
                                <input type="number" name="sizes[0][stock]" placeholder="10" value="0" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Display Order</label>
                                <input type="number" name="sizes[0][display_order]" placeholder="0" value="0" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                            </div>
                            <div class="col-span-5">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="sizes[0][is_default]" name="sizes[0][is_default]" type="checkbox" class="default-size-checkbox focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded" value="1">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="sizes[0][is_default]" class="font-medium text-gray-700 dark:text-gray-300">Default Size</label>
                                        <p class="text-gray-500 dark:text-gray-400">Set as the default size option</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="mt-6">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-save mr-2"></i> Save Sizes
                </button>
            </div>
        </form>
    </div>

    <!-- Branches Panel -->
    <div id="branches-panel" class="tab-panel bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6 hidden">
        <!-- Multi-branch product settings -->
    </div>
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/mdbassit/Coloris@latest/dist/coloris.min.css"/>
<style>
    /* Color swatch styling */
    .color-swatch {
        cursor: pointer;
        transition: transform 0.2s;
    }

    .color-swatch:hover {
        transform: scale(1.1) translateY(-50%) !important;
    }

    /* Coloris customization */
    .clr-field button {
        width: 28px;
        height: 28px;
        left: auto;
        right: 8px;
        border-radius: 5px;
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/gh/mdbassit/Coloris@latest/dist/coloris.min.js"></script>
<script src="{{ asset('js/color-picker.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab switching
        const tabLinks = document.querySelectorAll('.tab-link');
        const tabPanels = document.querySelectorAll('.tab-panel');

        tabLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();

                // Remove active class from all tabs
                tabLinks.forEach(tab => {
                    tab.classList.remove('border-indigo-500', 'text-indigo-600', 'dark:text-indigo-400');
                    tab.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300', 'dark:text-gray-400', 'dark:hover:text-gray-300');
                });

                // Add active class to clicked tab
                this.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300', 'dark:text-gray-400', 'dark:hover:text-gray-300');
                this.classList.add('border-indigo-500', 'text-indigo-600', 'dark:text-indigo-400');

                // Hide all panels
                tabPanels.forEach(panel => {
                    panel.classList.add('hidden');
                });

                // Show target panel
                const targetPanel = document.getElementById(this.getAttribute('data-target'));
                targetPanel.classList.remove('hidden');
            });
        });

        // Add specification
        const addSpecificationBtn = document.getElementById('add-specification');
        const specificationsContainer = document.getElementById('specifications-container');

        addSpecificationBtn.addEventListener('click', function() {
            const items = specificationsContainer.querySelectorAll('.specification-item');
            const newIndex = items.length;

            const newItem = document.createElement('div');
            newItem.className = 'specification-item grid grid-cols-12 gap-4 items-center';
            newItem.innerHTML = `
                <div class="col-span-5">
                    <input type="text" name="specifications[${newIndex}][key]" placeholder="Key (e.g. Material)" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                </div>
                <div class="col-span-5">
                    <input type="text" name="specifications[${newIndex}][value]" placeholder="Value (e.g. Cotton)" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                </div>
                <div class="col-span-1">
                    <input type="number" name="specifications[${newIndex}][display_order]" placeholder="Order" value="${newIndex}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                </div>
                <div class="col-span-1 text-center">
                    <button type="button" class="remove-item text-red-500 hover:text-red-700">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;

            specificationsContainer.appendChild(newItem);
            setupRemoveButtons();
        });

        // Add color
        const addColorBtn = document.getElementById('add-color');
        const colorsContainer = document.getElementById('colors-container');

        if (addColorBtn) {
            addColorBtn.addEventListener('click', function() {
                const items = colorsContainer.querySelectorAll('.color-item');
                const newIndex = items.length;

                const newItem = document.createElement('div');
                newItem.className = 'color-item border border-gray-200 dark:border-gray-700 rounded-lg p-4';
                newItem.innerHTML = `
                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-3">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Color Name</label>
                            <select name="colors[${newIndex}][name]" class="color-name-select focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" required>
                                <option value="">Select Color</option>
                                <option value="Red">Red</option>
                                <option value="Crimson">Crimson</option>
                                <option value="FireBrick">FireBrick</option>
                                <option value="DarkRed">DarkRed</option>
                                <option value="IndianRed">IndianRed</option>
                                <option value="LightCoral">LightCoral</option>
                                <option value="Salmon">Salmon</option>
                                <option value="DarkSalmon">DarkSalmon</option>
                                <option value="LightSalmon">LightSalmon</option>
                                <option value="Orange">Orange</option>
                                <option value="DarkOrange">DarkOrange</option>
                                <option value="Coral">Coral</option>
                                <option value="Tomato">Tomato</option>
                                <option value="Gold">Gold</option>
                                <option value="Yellow">Yellow</option>
                                <option value="LightYellow">LightYellow</option>
                                <option value="LemonChiffon">LemonChiffon</option>
                                <option value="Khaki">Khaki</option>
                                <option value="DarkKhaki">DarkKhaki</option>
                                <option value="Green">Green</option>
                                <option value="Lime">Lime</option>
                                <option value="ForestGreen">ForestGreen</option>
                                <option value="DarkGreen">DarkGreen</option>
                                <option value="SeaGreen">SeaGreen</option>
                                <option value="MediumSeaGreen">MediumSeaGreen</option>
                                <option value="LightGreen">LightGreen</option>
                                <option value="PaleGreen">PaleGreen</option>
                                <option value="SpringGreen">SpringGreen</option>
                                <option value="MediumSpringGreen">MediumSpringGreen</option>
                                <option value="YellowGreen">YellowGreen</option>
                                <option value="Olive">Olive</option>
                                <option value="DarkOliveGreen">DarkOliveGreen</option>
                                <option value="Blue">Blue</option>
                                <option value="MediumBlue">MediumBlue</option>
                                <option value="DarkBlue">DarkBlue</option>
                                <option value="Navy">Navy</option>
                                <option value="SkyBlue">SkyBlue</option>
                                <option value="LightSkyBlue">LightSkyBlue</option>
                                <option value="DeepSkyBlue">DeepSkyBlue</option>
                                <option value="DodgerBlue">DodgerBlue</option>
                                <option value="SteelBlue">SteelBlue</option>
                                <option value="CornflowerBlue">CornflowerBlue</option>
                                <option value="RoyalBlue">RoyalBlue</option>
                                <option value="LightBlue">LightBlue</option>
                                <option value="PowderBlue">PowderBlue</option>
                                <option value="Purple">Purple</option>
                                <option value="MediumPurple">MediumPurple</option>
                                <option value="BlueViolet">BlueViolet</option>
                                <option value="Violet">Violet</option>
                                <option value="Orchid">Orchid</option>
                                <option value="Magenta">Magenta</option>
                                <option value="Fuchsia">Fuchsia</option>
                                <option value="DeepPink">DeepPink</option>
                                <option value="HotPink">HotPink</option>
                                <option value="LightPink">LightPink</option>
                                <option value="PaleVioletRed">PaleVioletRed</option>
                                <option value="Brown">Brown</option>
                                <option value="SaddleBrown">SaddleBrown</option>
                                <option value="Sienna">Sienna</option>
                                <option value="Chocolate">Chocolate</option>
                                <option value="Peru">Peru</option>
                                <option value="Tan">Tan</option>
                                <option value="RosyBrown">RosyBrown</option>
                                <option value="SandyBrown">SandyBrown</option>
                                <option value="BurlyWood">BurlyWood</option>
                                <option value="Wheat">Wheat</option>
                                <option value="NavajoWhite">NavajoWhite</option>
                                <option value="Black">Black</option>
                                <option value="DimGray">DimGray</option>
                                <option value="Gray">Gray</option>
                                <option value="DarkGray">DarkGray</option>
                                <option value="Silver">Silver</option>
                                <option value="LightGray">LightGray</option>
                                <option value="Gainsboro">Gainsboro</option>
                                <option value="WhiteSmoke">WhiteSmoke</option>
                                <option value="White">White</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Color Code</label>
                            <input type="text" name="colors[${newIndex}][color_code]" placeholder="#FF0000" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Price Adjustment</label>
                            <input type="number" step="0.01" name="colors[${newIndex}][price_adjustment]" placeholder="0.00" value="0" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Stock</label>
                            <input type="number" name="colors[${newIndex}][stock]" placeholder="10" value="0" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Display Order</label>
                            <input type="number" name="colors[${newIndex}][display_order]" placeholder="0" value="${newIndex}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div>
                        <div class="col-span-1 flex items-end justify-center">
                            <button type="button" class="remove-item text-red-500 hover:text-red-700">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mt-4 grid grid-cols-12 gap-4">
                        <div class="col-span-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Color Image</label>
                            <input type="file" name="color_images[${newIndex}]" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-gray-700 dark:file:text-gray-200">
                            <input type="hidden" name="colors[${newIndex}][image]" value="">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Default Color</label>
                            <div class="mt-1">
                                <input type="checkbox" name="colors[${newIndex}][is_default]" value="1" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            </div>
                        </div>
                    </div>
                `;

                colorsContainer.appendChild(newItem);
                setupRemoveButtons();

                // Enhance the new color dropdown with visual styling
                if (window.enhancedColorSelection) {
                    window.enhancedColorSelection.handleDynamicColorItem(newItem);
                }
            });
        }

        // Add size
        const addSizeBtn = document.getElementById('add-size');
        const sizesContainer = document.getElementById('sizes-container');

        if (addSizeBtn && sizesContainer) {
            addSizeBtn.addEventListener('click', function() {
                const items = sizesContainer.querySelectorAll('.size-item');
                const newIndex = items.length;

                const newItem = document.createElement('div');
                newItem.className = 'size-item border border-gray-200 dark:border-gray-700 rounded-lg p-4';
                newItem.innerHTML = `
                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-3">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Size Category</label>
                            <select class="size-category-select focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" required>
                                <option value="">Select Category</option>
                                <option value="clothes">Clothes</option>
                                <option value="shoes">Shoes</option>
                                <option value="hats">Hats</option>
                            </select>
                        </div>
                        <div class="size-selection-container col-span-6 grid grid-cols-6 gap-4">
                            <div class="col-span-6 flex items-center justify-center text-gray-500 dark:text-gray-400">
                                <p>Please select a size category</p>
                            </div>
                            <input type="hidden" name="sizes[${newIndex}][name]" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" required>
                            <input type="hidden" name="sizes[${newIndex}][value]" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Price Adjustment</label>
                            <input type="number" step="0.01" name="sizes[${newIndex}][price_adjustment]" placeholder="0.00" value="0" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div>
                        <div class="col-span-1 flex items-end justify-center">
                            <button type="button" class="remove-item text-red-500 hover:text-red-700">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-12 gap-4 mt-4">
                        <div class="col-span-3">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Additional Info</label>
                            <input type="text" name="sizes[${newIndex}][additional_info]" placeholder="Foot length, age group, etc." class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Stock</label>
                            <input type="number" name="sizes[${newIndex}][stock]" placeholder="10" value="0" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Display Order</label>
                            <input type="number" name="sizes[${newIndex}][display_order]" placeholder="0" value="${newIndex}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div>
                        <div class="col-span-5">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="sizes[${newIndex}][is_default]" name="sizes[${newIndex}][is_default]" type="checkbox" class="default-size-checkbox focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded" value="1">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="sizes[${newIndex}][is_default]" class="font-medium text-gray-700 dark:text-gray-300">Default Size</label>
                                    <p class="text-gray-500 dark:text-gray-400">Set as the default size option</p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                sizesContainer.appendChild(newItem);
                setupRemoveButtons();
            });
        }

        // Remove item functionality
        function setupRemoveButtons() {
            document.querySelectorAll('.remove-item').forEach(button => {
                button.addEventListener('click', function() {
                    const item = this.closest('.specification-item, .color-item, .size-item, .branch-item');
                    if (item) {
                        item.remove();
                    }
                });
            });
        }

        // Initialize remove buttons
        setupRemoveButtons();
    });
</script>
<script src="{{ asset('js/enhanced-size-selection.js') }}"></script>
<script src="{{ asset('js/enhanced-color-selection.js') }}"></script>
<script src="{{ asset('js/dynamic-color-size-management.js') }}"></script>
@endsection
