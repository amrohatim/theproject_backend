@extends('layouts.dashboard')

@section('title', __('provider.my_profile'))
@section('page-title', __('provider.my_profile'))

@section('content')
<div class="container mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ __('provider.my_profile') }}</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('provider.manage_personal_info') }}</p>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-700">
            <div class="flex items-center gap-2 font-semibold">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Profile Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide">{{ __('provider.profile_information') }}</h3>
            </div>
            <form action="{{ route('provider.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="flex flex-col items-center text-center gap-3">
                    <div class="relative">
                        <div class="h-24 w-24 rounded-full overflow-hidden border border-gray-200 dark:border-gray-700">
                            @if($user->profile_image)
                                <img src="@userProfileImage($user->profile_image)" alt="{{ $user->name }}" class="h-full w-full object-cover">
                            @else
                                <div class="h-full w-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                    <i class="fas fa-user text-gray-400 text-2xl"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                    </div>
                    <div>
                        <label for="profile_image" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-xs font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                            <i class="fas fa-camera mr-2"></i> {{ __('provider.change_photo') }}
                        </label>
                        <input type="file" id="profile_image" name="profile_image" class="hidden" accept="image/*">
                        <div id="file-name" class="mt-2 text-xs text-gray-500 dark:text-gray-400"></div>
                    </div>
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('provider.full_name') }}</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                        class="mt-2 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('provider.email_address') }}</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="mt-2 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('provider.phone_number') }}</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                        class="mt-2 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                    @error('phone')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-save mr-2"></i> {{ __('provider.save_changes') }}
                </button>
            </form>
        </div>

        <!-- Change Password + Store Settings -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide mb-6">{{ __('provider.change_password') }}</h3>
                <form action="{{ route('provider.profile.update-password') }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('provider.current_password') }}</label>
                        <input type="password" id="current_password" name="current_password" required
                            class="mt-2 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                        @error('current_password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('provider.new_password') }}</label>
                        <input type="password" id="password" name="password" required
                            class="mt-2 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('provider.confirm_new_password') }}</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                            class="mt-2 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-key mr-2"></i> {{ __('provider.update_password') }}
                    </button>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide mb-4">{{ __('provider.store_settings') }}</h3>
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 mb-4">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('provider.your_store') }}</h4>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('provider.customize_store_description') }}</p>
                    <a href="/provider/settings" class="mt-3 inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-xs font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <i class="fas fa-cog mr-2"></i> {{ __('provider.store_settings') }}
                    </a>
                </div>

                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('provider.quick_links') }}</h4>
                    <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <a href="{{ route('provider.provider-products.index') }}" class="flex items-center gap-2 rounded-md border border-gray-200 dark:border-gray-700 px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <i class="fas fa-boxes text-gray-500 dark:text-gray-400"></i>
                            {{ __('provider.my_products') }}
                        </a>
                        <a href="{{ route('provider.orders.index') }}" class="flex items-center gap-2 rounded-md border border-gray-200 dark:border-gray-700 px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <i class="fas fa-receipt text-gray-500 dark:text-gray-400"></i>
                            {{ __('provider.my_orders') }}
                        </a>
                        <a href="#" class="flex items-center gap-2 rounded-md border border-gray-200 dark:border-gray-700 px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <i class="fas fa-wallet text-gray-500 dark:text-gray-400"></i>
                            {{ __('provider.payments') }}
                        </a>
                        <a href="#" class="flex items-center gap-2 rounded-md border border-gray-200 dark:border-gray-700 px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <i class="fas fa-chart-bar text-gray-500 dark:text-gray-400"></i>
                            {{ __('provider.analytics') }}
                        </a>
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
        $('#profile_image').change(function() {
            var fileName = $(this).val().split('\\').pop();
            $('#file-name').text(fileName ? fileName : '');

            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('.profile-image-container img').attr('src', e.target.result);
                    $('.profile-image-container div').hide();
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
</script>
@endsection
