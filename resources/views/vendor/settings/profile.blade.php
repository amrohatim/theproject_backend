@extends('layouts.dashboard')

@section('title', __('vendor.profile_settings'))
@section('page-title', __('vendor.profile_settings'))

@section('content')
<div class="container mx-auto" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('vendor.profile_settings') }}</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">{{ __('vendor.profile_settings_description') }}</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('vendor.settings') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i> {{ __('vendor.back_to_settings') }}
            </a>
        </div>
    </div>

    <!-- Profile Information -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700 mb-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('vendor.profile_information') }}</h3>
        <form action="#" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Profile Picture -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('vendor.profile_picture') }}</label>
                    <div class="mt-1 flex items-center">
                        <div class="w-20 h-20 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-700">
                            @if(auth()->user()->profile_image)
                                <img src="{{ auth()->user()->profile_image }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400 dark:text-gray-500">
                                    <i class="fas fa-user text-3xl"></i>
                                </div>
                            @endif
                        </div>
                        <div class="{{ app()->getLocale() === 'ar' ? 'mr-5' : 'ml-5' }}">
                            <label for="profile_image" class="cursor-pointer inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('vendor.change') }}
                            </label>
                            <input id="profile_image" name="profile_image" type="file" class="hidden" accept="image/*">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('vendor.file_format_info') }}</p>
                        </div>
                    </div>
                    @error('profile_image')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('vendor.name') }}</label>
                    <input type="text" name="name" id="name" value="{{ auth()->user()->name }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('vendor.email') }}</label>
                    <input type="email" name="email" id="email" value="{{ auth()->user()->email }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('vendor.phone') }}</label>
                    <input type="text" name="phone" id="phone" value="{{ auth()->user()->phone ?? '' }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address -->
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('vendor.address') }}</label>
                    <input type="text" name="address" id="address" value="{{ auth()->user()->address ?? '' }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                    @error('address')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bio -->
                <div class="md:col-span-2">
                    <label for="bio" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('vendor.bio') }}</label>
                    <textarea name="bio" id="bio" rows="4" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">{{ auth()->user()->bio ?? '' }}</textarea>
                    @error('bio')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-save {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i> {{ __('vendor.save_changes') }}
                </button>
            </div>
        </form>
    </div>

    <!-- Social Media Links -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Social Media Links</h3>
        <form action="#" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Facebook -->
                <div>
                    <label for="facebook" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Facebook</label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 sm:text-sm">
                            <i class="fab fa-facebook-f"></i>
                        </span>
                        <input type="text" name="facebook" id="facebook" value="{{ auth()->user()->social_links['facebook'] ?? '' }}" class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-r-md sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>
                
                <!-- Twitter -->
                <div>
                    <label for="twitter" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Twitter</label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 sm:text-sm">
                            <i class="fab fa-twitter"></i>
                        </span>
                        <input type="text" name="twitter" id="twitter" value="{{ auth()->user()->social_links['twitter'] ?? '' }}" class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-r-md sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>
                
                <!-- Instagram -->
                <div>
                    <label for="instagram" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Instagram</label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 sm:text-sm">
                            <i class="fab fa-instagram"></i>
                        </span>
                        <input type="text" name="instagram" id="instagram" value="{{ auth()->user()->social_links['instagram'] ?? '' }}" class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-r-md sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>
                
                <!-- LinkedIn -->
                <div>
                    <label for="linkedin" class="block text-sm font-medium text-gray-700 dark:text-gray-300">LinkedIn</label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 sm:text-sm">
                            <i class="fab fa-linkedin-in"></i>
                        </span>
                        <input type="text" name="linkedin" id="linkedin" value="{{ auth()->user()->social_links['linkedin'] ?? '' }}" class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-r-md sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-save mr-2"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
