@extends('layouts.merchant')

@section('title', 'Global Settings')
@section('header', 'Global Settings')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 p-6">
    <div class="max-w-6xl mx-auto space-y-8">
        <!-- Header -->
        <div class="text-center space-y-2">
            <h1 class="text-4xl font-bold text-slate-900">Global Settings</h1>
            <p class="text-slate-600">Manage your business information and preferences</p>
        </div>

        <!-- Success Alert -->
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        <!-- Business Information -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-100 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-building text-blue-600 text-sm"></i>
                    </div>
                    Business Information
                </h2>
                <p class="text-gray-600 text-sm mt-1">Update your business details and settings</p>
            </div>
            <div class="p-8 space-y-6">

                <form action="{{ route('merchant.settings.global.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Business Name -->
                        <div class="space-y-2">
                            <label for="business_name" class="text-slate-700 font-medium flex items-center gap-2">
                                <i class="fas fa-building w-4 h-4"></i>
                                Business Name *
                            </label>
                            <input type="text"
                                   class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all @error('business_name') border-red-500 @enderror"
                                   id="business_name"
                                   name="business_name"
                                   value="{{ old('business_name', $merchant->business_name ?? '') }}"
                                   required>
                            @error('business_name')
                                <div class="text-red-600 text-sm">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Business Type -->
                        <div class="space-y-2">
                            <label for="business_type" class="text-slate-700 font-medium flex items-center gap-2">
                                <i class="fas fa-tag w-4 h-4"></i>
                                Business Type
                            </label>
                            <select class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all @error('business_type') border-red-500 @enderror"
                                    id="business_type"
                                    name="business_type">
                                <option value="">Select Business Type</option>
                                <option value="retail" {{ old('business_type', $merchant->business_type ?? '') == 'retail' ? 'selected' : '' }}>Retail</option>
                                <option value="restaurant" {{ old('business_type', $merchant->business_type ?? '') == 'restaurant' ? 'selected' : '' }}>Restaurant</option>
                                <option value="services" {{ old('business_type', $merchant->business_type ?? '') == 'services' ? 'selected' : '' }}>Services</option>
                                <option value="fashion" {{ old('business_type', $merchant->business_type ?? '') == 'fashion' ? 'selected' : '' }}>Fashion</option>
                                <option value="electronics" {{ old('business_type', $merchant->business_type ?? '') == 'electronics' ? 'selected' : '' }}>Electronics</option>
                                <option value="health" {{ old('business_type', $merchant->business_type ?? '') == 'health' ? 'selected' : '' }}>Health & Beauty</option>
                                <option value="other" {{ old('business_type', $merchant->business_type ?? '') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('business_type')
                                <div class="text-red-600 text-sm">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Website -->
                        <div class="space-y-2">
                            <label for="website" class="text-slate-700 font-medium flex items-center gap-2">
                                <i class="fas fa-globe w-4 h-4"></i>
                                Website URL
                            </label>
                            <input type="url"
                                   class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all @error('website') border-red-500 @enderror"
                                   id="website"
                                   name="website"
                                   value="{{ old('website', $merchant->website ?? '') }}"
                                   placeholder="https://example.com">
                            @error('website')
                                <div class="text-red-600 text-sm">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- City -->
                        <div class="space-y-2">
                            <label for="city" class="text-slate-700 font-medium flex items-center gap-2">
                                <i class="fas fa-map-pin w-4 h-4"></i>
                                City
                            </label>
                            <input type="text"
                                   class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all @error('city') border-red-500 @enderror"
                                   id="city"
                                   name="city"
                                   value="{{ old('city', $merchant->city ?? '') }}"
                                   placeholder="Enter your city">
                            @error('city')
                                <div class="text-red-600 text-sm">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Emirate -->
                        <div class="space-y-2">
                            <label for="emirate" class="text-slate-700 font-medium flex items-center gap-2">
                                <i class="fas fa-map-pin w-4 h-4"></i>
                                Emirate
                            </label>
                            <select class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all @error('emirate') border-red-500 @enderror"
                                    id="emirate"
                                    name="emirate">
                                <option value="">Select Emirate</option>
                                <option value="Abu Dhabi" {{ old('emirate', $merchant->emirate ?? '') == 'Abu Dhabi' ? 'selected' : '' }}>Abu Dhabi</option>
                                <option value="Dubai" {{ old('emirate', $merchant->emirate ?? '') == 'Dubai' ? 'selected' : '' }}>Dubai</option>
                                <option value="Sharjah" {{ old('emirate', $merchant->emirate ?? '') == 'Sharjah' ? 'selected' : '' }}>Sharjah</option>
                                <option value="Ajman" {{ old('emirate', $merchant->emirate ?? '') == 'Ajman' ? 'selected' : '' }}>Ajman</option>
                                <option value="Umm Al Quwain" {{ old('emirate', $merchant->emirate ?? '') == 'Umm Al Quwain' ? 'selected' : '' }}>Umm Al Quwain</option>
                                <option value="Ras Al Khaimah" {{ old('emirate', $merchant->emirate ?? '') == 'Ras Al Khaimah' ? 'selected' : '' }}>Ras Al Khaimah</option>
                                <option value="Fujairah" {{ old('emirate', $merchant->emirate ?? '') == 'Fujairah' ? 'selected' : '' }}>Fujairah</option>
                            </select>
                            @error('emirate')
                                <div class="text-red-600 text-sm">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="space-y-2">
                            <label for="address" class="text-slate-700 font-medium flex items-center gap-2">
                                <i class="fas fa-map-pin w-4 h-4"></i>
                                Business Address
                            </label>
                            <input type="text"
                                   class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all @error('address') border-red-500 @enderror"
                                   id="address"
                                   name="address"
                                   value="{{ old('address', $merchant->address ?? '') }}"
                                   placeholder="Enter your business address">
                            @error('address')
                                <div class="text-red-600 text-sm">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Logo Upload -->
                    <div class="space-y-4">
                        <label class="text-slate-700 font-medium flex items-center gap-2">
                            <i class="fas fa-image w-4 h-4"></i>
                            Business Logo
                        </label>

                        <div class="flex items-center gap-6">
                            @if($merchant && $merchant->getRawOriginal('logo'))
                                <!-- Current Logo Display -->
                                <div class="relative flex-shrink-0">
                                    <img src="{{ $merchant->logo }}" alt="Current Logo"
                                         class="w-24 h-24 object-cover rounded-lg border-2 border-slate-200 shadow-md"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';"
                                         id="current-logo-img">
                                   
                                    <!-- Fallback for broken image -->
                                    <div style="display: none;" class="w-24 h-24 bg-slate-100 border-2 border-slate-200 rounded-lg flex items-center justify-center text-slate-400 shadow-md">
                                        <i class="fas fa-exclamation-triangle w-6 h-6"></i>
                                    </div>
                                </div>
                            @endif

                            <!-- Upload Area -->
                            <div class="flex-1">
                                <div class="border-2 border-dashed border-slate-300 rounded-lg p-6 text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50/50 transition-all duration-200"
                                     onclick="document.getElementById('logo').click()"
                                     id="logo-upload-area">
                                    <i class="fas fa-upload text-slate-400 text-2xl mb-2 block"></i>
                                    <p class="text-sm text-slate-600" id="upload-text">
                                        @if($merchant && $merchant->getRawOriginal('logo'))
                                            Click to change logo
                                        @else
                                            Click to upload logo
                                        @endif
                                    </p>
                                    <p class="text-xs text-slate-500 mt-1">Max size: 2MB. Formats: JPEG, PNG, JPG, GIF</p>
                                </div>

                                <input type="file"
                                       class="hidden @error('logo') border-red-500 @enderror"
                                       id="logo"
                                       name="logo"
                                       accept="image/*">

                                @error('logo')
                                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="space-y-2">
                        <label for="description" class="text-slate-700 font-medium flex items-center gap-2">
                            <i class="fas fa-file-text w-4 h-4"></i>
                            Business Description
                        </label>
                        <textarea class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all min-h-[100px] @error('description') border-red-500 @enderror"
                                  id="description"
                                  name="description"
                                  placeholder="Describe your business, products, and services...">{{ old('description', $merchant->description ?? '') }}</textarea>
                        @error('description')
                            <div class="text-red-600 text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center mt-5 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm">
                            <i class="fas fa-save w-4 h-4 mr-2"></i>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delivery Settings -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border-b border-gray-100 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-truck text-emerald-600 text-sm"></i>
                    </div>
                    Delivery Settings
                </h2>
                <p class="text-gray-600 text-sm mt-1">Configure your delivery options and fees</p>
            </div>
            <div class="p-8 space-y-6">
                <form action="{{ route('merchant.settings.global.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Delivery Capability -->
                    <div class="flex items-center space-x-3">
                        <input type="checkbox"
                               id="delivery_capability"
                               name="delivery_capability"
                               value="1"
                               {{ old('delivery_capability', $merchant->delivery_capability ?? false) ? 'checked' : '' }}
                               class="w-4 h-4 text-emerald-600 bg-gray-100 border-gray-300 rounded focus:ring-emerald-500 focus:ring-2">
                        <label for="delivery_capability" class="text-slate-700 font-medium flex items-center gap-2">
                            <i class="fas fa-truck w-4 h-4"></i>
                            We offer delivery services
                        </label>
                    </div>

                    <!-- Delivery Fees (shown when delivery is enabled) -->
                    <div id="delivery-fees-section" style="display: {{ old('delivery_capability', $merchant->delivery_capability ?? false) ? 'block' : 'none' }};">
                        <div class="space-y-4 p-6 bg-slate-50 rounded-lg border border-slate-200">
                            <h4 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                                <i class="fas fa-dollar-sign w-5 h-5"></i>
                                Delivery Fees by Emirate (AED)
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <!-- Dubai -->
                                <div class="space-y-2">
                                    <label class="text-slate-700 font-medium">Dubai</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-500 text-sm">AED</span>
                                        <input type="number"
                                               class="w-full pl-12 pr-4 py-3 border border-slate-300 rounded-lg focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all"
                                               id="dubai_delivery_fee"
                                               name="delivery_fees[dubai]"
                                               value="{{ old('delivery_fees.dubai', $merchant->delivery_fees['dubai'] ?? '') }}"
                                               step="0.01"
                                               min="0">
                                    </div>
                                </div>

                                <!-- Abu Dhabi -->
                                <div class="space-y-2">
                                    <label class="text-slate-700 font-medium">Abu Dhabi</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-500 text-sm">AED</span>
                                        <input type="number"
                                               class="w-full pl-12 pr-4 py-3 border border-slate-300 rounded-lg focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all"
                                               id="abu_dhabi_delivery_fee"
                                               name="delivery_fees[abu_dhabi]"
                                               value="{{ old('delivery_fees.abu_dhabi', $merchant->delivery_fees['abu_dhabi'] ?? '') }}"
                                               step="0.01"
                                               min="0">
                                    </div>
                                </div>

                                <!-- Sharjah -->
                                <div class="space-y-2">
                                    <label class="text-slate-700 font-medium">Sharjah</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-500 text-sm">AED</span>
                                        <input type="number"
                                               class="w-full pl-12 pr-4 py-3 border border-slate-300 rounded-lg focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all"
                                               id="sharjah_delivery_fee"
                                               name="delivery_fees[sharjah]"
                                               value="{{ old('delivery_fees.sharjah', $merchant->delivery_fees['sharjah'] ?? '') }}"
                                               step="0.01"
                                               min="0">
                                    </div>
                                </div>

                                <!-- Ajman -->
                                <div class="space-y-2">
                                    <label class="text-slate-700 font-medium">Ajman</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-500 text-sm">AED</span>
                                        <input type="number"
                                               class="w-full pl-12 pr-4 py-3 border border-slate-300 rounded-lg focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all"
                                               id="ajman_delivery_fee"
                                               name="delivery_fees[ajman]"
                                               value="{{ old('delivery_fees.ajman', $merchant->delivery_fees['ajman'] ?? '') }}"
                                               step="0.01"
                                               min="0">
                                    </div>
                                </div>

                                <!-- Ras Al Khaimah -->
                                <div class="space-y-2">
                                    <label class="text-slate-700 font-medium">Ras Al Khaimah</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-500 text-sm">AED</span>
                                        <input type="number"
                                               class="w-full pl-12 pr-4 py-3 border border-slate-300 rounded-lg focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all"
                                               id="ras_al_khaimah_delivery_fee"
                                               name="delivery_fees[ras_al_khaimah]"
                                               value="{{ old('delivery_fees.ras_al_khaimah', $merchant->delivery_fees['ras_al_khaimah'] ?? '') }}"
                                               step="0.01"
                                               min="0">
                                    </div>
                                </div>

                                <!-- Fujairah -->
                                <div class="space-y-2">
                                    <label class="text-slate-700 font-medium">Fujairah</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-500 text-sm">AED</span>
                                        <input type="number"
                                               class="w-full pl-12 pr-4 py-3 border border-slate-300 rounded-lg focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all"
                                               id="fujairah_delivery_fee"
                                               name="delivery_fees[fujairah]"
                                               value="{{ old('delivery_fees.fujairah', $merchant->delivery_fees['fujairah'] ?? '') }}"
                                               step="0.01"
                                               min="0">
                                    </div>
                                </div>

                                <!-- Umm Al Quwain -->
                                <div class="space-y-2">
                                    <label class="text-slate-700 font-medium">Umm Al Quwain</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-500 text-sm">AED</span>
                                        <input type="number"
                                               class="w-full pl-12 pr-4 py-3 border border-slate-300 rounded-lg focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all"
                                               id="umm_al_quwain_delivery_fee"
                                               name="delivery_fees[umm_al_quwain]"
                                               value="{{ old('delivery_fees.umm_al_quwain', $merchant->delivery_fees['umm_al_quwain'] ?? '') }}"
                                               step="0.01"
                                               min="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center mt-5 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm">
                            <i class="fas fa-save w-4 h-4 mr-2"></i>
                            Save Delivery Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @if($merchant)
        <!-- Business Status -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border-b border-gray-100 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-indigo-600 text-sm"></i>
                    </div>
                    Business Status
                </h2>
                <p class="text-gray-600 text-sm mt-1">Your current business verification and status information</p>
            </div>
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-3 border-b border-slate-200">
                            <span class="text-slate-600">Business Status:</span>
                            @if($merchant->is_verified)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">
                                    <i class="fas fa-check-circle w-3 h-3 mr-1"></i>
                                    Verified
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-500/10 text-amber-600 border border-amber-500/20">
                                    <i class="fas fa-clock w-3 h-3 mr-1"></i>
                                    Pending Verification
                                </span>
                            @endif
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-slate-200">
                            <span class="text-slate-600">Registration Date:</span>
                            <span class="font-medium text-slate-800">
                                {{ $merchant->created_at->format('M d, Y') }}
                            </span>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-3 border-b border-slate-200">
                            <span class="text-slate-600">Delivery Service:</span>
                            @if($merchant->delivery_capability)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">
                                    <i class="fas fa-check-circle w-3 h-3 mr-1"></i>
                                    Enabled
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-500/10 text-slate-600 border border-slate-500/20">
                                    <i class="fas fa-times w-3 h-3 mr-1"></i>
                                    Disabled
                                </span>
                            @endif
                        </div>
                        <div class="flex justify-between items-center py-3">
                            <span class="text-slate-600">Last Updated:</span>
                            <span class="font-medium text-slate-800">
                                {{ $merchant->updated_at->format('M d, Y') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

        @if($merchant)
        <!-- License Management -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-purple-50 to-pink-50 border-b border-gray-100 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shield-alt text-purple-600 text-sm"></i>
                    </div>
                    License Management
                </h2>
                <p class="text-gray-600 text-sm mt-1">Manage your business license and verification status</p>
            </div>
            <div class="p-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Current License Status -->
                    <div class="space-y-6">
                        <h4 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                            <i class="fas fa-shield-alt w-5 h-5"></i>
                            Current License Status
                        </h4>

                        <div class="space-y-4">
                            @php
                                $licenseStatus = $merchant->getLicenseStatusWithColor();
                            @endphp

                            <div class="flex justify-between items-center py-3 border-b border-slate-200">
                                <span class="text-slate-600">License Status:</span>
                                @if($licenseStatus['text'] === 'Approved')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">
                                        <i class="fas fa-check-circle w-3 h-3 mr-1"></i>
                                        Approved
                                    </span>
                                @elseif($licenseStatus['text'] === 'Rejected')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-500/10 text-red-600 border border-red-500/20">
                                        <i class="fas fa-times w-3 h-3 mr-1"></i>
                                        Rejected
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-500/10 text-amber-600 border border-amber-500/20">
                                        <i class="fas fa-clock w-3 h-3 mr-1"></i>
                                        Under Review
                                    </span>
                                @endif
                            </div>

                            @if($merchant->license_expiry_date)
                                <div class="flex justify-between items-center py-3 border-b border-slate-200">
                                    <span class="text-slate-600">Expiry Date:</span>
                                    <div class="text-right">
                                        <div class="font-medium text-slate-800">
                                            {{ $merchant->license_expiry_date->format('M d, Y') }}
                                        </div>
                                        @if($merchant->license_expiry_date->isFuture())
                                            <div class="text-xs text-slate-500">
                                                {{ $merchant->daysUntilLicenseExpiration() }} days remaining
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if($merchant->license_file)
                                <div class="flex justify-between items-center py-3 border-b border-slate-200">
                                    <span class="text-slate-600">Current License:</span>
                                    <a href="{{ $merchant->license_file_url }}" target="_blank"
                                       class="inline-flex items-center px-3 py-1 text-sm text-blue-600 border border-blue-200 rounded-md hover:bg-blue-50 transition-colors">
                                        <i class="fas fa-file-text w-3 h-3 mr-1"></i>
                                        View License
                                    </a>
                                </div>
                            @endif
                        </div>

                        @if($merchant->license_status === 'rejected' && $merchant->license_rejection_reason)
                            <div class="bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-exclamation-triangle w-4 h-4 mr-2"></i>
                                    <strong>Rejection Reason:</strong>
                                </div>
                                <p class="text-sm">{{ $merchant->license_rejection_reason }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- License Upload -->
                    <div class="space-y-6">
                        <h4 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                            <i class="fas fa-upload w-5 h-5"></i>
                            Upload New License
                        </h4>

                        <form action="{{ route('merchant.settings.license.update') }}" method="POST" enctype="multipart/form-data" id="license-form">
                            @csrf
                            @method('PUT')

                            <!-- File Upload Area -->
                            <div class="border-2 border-dashed border-slate-300 rounded-lg p-8 text-center cursor-pointer hover:border-purple-400 hover:bg-purple-50/50 transition-all duration-200"
                                 onclick="document.getElementById('license_file').click()">
                                <div id="upload-content">
                                    <i class="fas fa-upload text-slate-400 text-3xl mb-4"></i>
                                    <p class="font-medium text-slate-800 mb-2">Click to select your license file</p>
                                    <p class="text-sm text-slate-600">PDF files only, max 5MB</p>
                                </div>
                                <div id="file-selected" class="hidden">
                                    <i class="fas fa-file-text text-emerald-500 text-3xl mb-4"></i>
                                    <p class="font-medium text-slate-800 mb-2" id="file-name"></p>
                                    <p class="text-sm text-slate-600" id="file-size"></p>
                                    <p class="text-xs text-emerald-600 flex items-center justify-center gap-1 mt-2">
                                        <i class="fas fa-check-circle w-3 h-3"></i>
                                        Ready to upload
                                    </p>
                                </div>
                            </div>
                            <input type="file" id="license_file" name="license_file" accept=".pdf" class="hidden">
                            @error('license_file')
                                <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
                            @enderror

                            <!-- License Expiry Date -->
                            <div class="space-y-2">
                                <label for="license_expiry_date" class="text-slate-700 font-medium flex items-center gap-2">
                                    <i class="fas fa-calendar w-4 h-4"></i>
                                    License Expiry Date *
                                </label>
                                <input type="date"
                                       class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all @error('license_expiry_date') border-red-500 @enderror"
                                       id="license_expiry_date"
                                       name="license_expiry_date"
                                       value="{{ old('license_expiry_date', $merchant->license_expiry_date?->format('Y-m-d')) }}"
                                       required>
                                @error('license_expiry_date')
                                    <div class="text-red-600 text-sm">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Upload Button -->
                            <button type="submit"
                                    class="w-full inline-flex mt-3 items-center justify-center px-6 py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm disabled:bg-gray-300 disabled:text-gray-500 disabled:cursor-not-allowed disabled:hover:bg-gray-300"
                                    id="license-submit-btn"
                                    disabled>
                                <i class="fas fa-upload w-4 h-4 mr-2"></i>
                                Upload License
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
// Global function to reset logo upload state
function resetLogoUpload() {
    const logoInput = document.getElementById('logo');
    const uploadText = document.getElementById('upload-text');
    const logoUploadArea = document.getElementById('logo-upload-area');
    const existingPreview = document.getElementById('new-logo-preview');

    if (logoInput) logoInput.value = '';
    if (existingPreview) existingPreview.remove();

    if (uploadText) {
        // Check if there's an existing logo to determine text
        const hasExistingLogo = document.getElementById('current-logo-img');
        uploadText.textContent = hasExistingLogo ? 'Click to change logo' : 'Click to upload logo';
    }

    if (logoUploadArea) {
        logoUploadArea.classList.remove('border-blue-400', 'bg-blue-50/50');
        logoUploadArea.classList.add('border-slate-300');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const deliveryCheckbox = document.getElementById('delivery_capability');
    const deliveryFeesSection = document.getElementById('delivery-fees-section');

    if (deliveryCheckbox && deliveryFeesSection) {
        deliveryCheckbox.addEventListener('change', function() {
            deliveryFeesSection.style.display = this.checked ? 'block' : 'none';
        });
    }

    // Logo preview functionality
    const logoInput = document.getElementById('logo');
    const uploadText = document.getElementById('upload-text');
    const logoUploadArea = document.getElementById('logo-upload-area');

    if (logoInput) {
        logoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Please select a valid image file (JPEG, PNG, JPG, or GIF).');
                    this.value = '';
                    return;
                }

                // Validate file size (2MB = 2048KB)
                if (file.size > 2048 * 1024) {
                    alert('File size must be less than 2MB.');
                    this.value = '';
                    return;
                }

                // Update upload area to show selected file
                if (uploadText) {
                    uploadText.textContent = file.name;
                }

                // Add visual feedback to upload area
                if (logoUploadArea) {
                    logoUploadArea.classList.add('border-blue-400', 'bg-blue-50/50');
                    logoUploadArea.classList.remove('border-slate-300');
                }

                // Create preview for new logo selection
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Remove existing preview if any
                    let existingPreview = document.getElementById('new-logo-preview');
                    if (existingPreview) {
                        existingPreview.remove();
                    }

                    // Create new preview
                    const previewDiv = document.createElement('div');
                    previewDiv.id = 'new-logo-preview';
                    previewDiv.className = 'mt-4 bg-gradient-to-br from-amber-50 to-orange-50 border-2 border-amber-200 rounded-lg p-4';
                    previewDiv.innerHTML = `
                        <div class="flex items-center gap-4">
                            <div class="relative flex-shrink-0">
                                <img src="${e.target.result}" alt="New Logo Preview"
                                     class="w-20 h-20 object-cover rounded-lg border-2 border-white shadow-md">
                                <div class="absolute -top-2 -right-2 bg-amber-500 text-white rounded-full w-6 h-6 flex items-center justify-center shadow-md">
                                    <i class="fas fa-clock w-3 h-3"></i>
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <h4 class="text-sm font-semibold text-slate-800">New Logo Selected</h4>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                        <i class="fas fa-clock w-2 h-2 mr-1"></i>
                                        Pending
                                    </span>
                                </div>
                                <p class="text-xs text-slate-600 mb-2">This logo will replace your current one when you save changes</p>
                                <button type="button"
                                        class="inline-flex items-center px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium rounded-md transition-colors duration-200"
                                        onclick="resetLogoUpload();">
                                    <i class="fas fa-times w-2 h-2 mr-1"></i>
                                    Cancel
                                </button>
                            </div>
                        </div>
                    `;

                    // Insert after the upload area
                    logoUploadArea.parentNode.insertBefore(previewDiv, logoUploadArea.nextSibling);
                };
                reader.readAsDataURL(file);
            } else {
                resetLogoUpload();
            }
        });
    }



    // License file upload functionality
    const licenseFileInput = document.getElementById('license_file');
    const uploadContent = document.getElementById('upload-content');
    const fileSelected = document.getElementById('file-selected');
    const fileName = document.getElementById('file-name');
    const fileSize = document.getElementById('file-size');
    const submitBtn = document.getElementById('license-submit-btn');

    if (licenseFileInput) {
        licenseFileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file type
                if (file.type !== 'application/pdf') {
                    alert('Please select a PDF file only');
                    this.value = '';
                    return;
                }

                // Validate file size (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('File size must be less than 5MB');
                    this.value = '';
                    return;
                }

                // Update UI
                uploadContent.classList.add('hidden');
                fileSelected.classList.remove('hidden');
                fileName.textContent = file.name;
                fileSize.textContent = `File size: ${(file.size / 1024 / 1024).toFixed(2)} MB`;

                // Enable submit button
                if (submitBtn) {
                    submitBtn.disabled = false;
                }
            } else {
                // Reset UI
                uploadContent.classList.remove('hidden');
                fileSelected.classList.add('hidden');
                if (submitBtn) {
                    submitBtn.disabled = true;
                }
            }
        });
    }
});
</script>
@endsection

