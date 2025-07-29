@extends('layouts.provider')

@section('title', 'My Profile')

@section('header', 'Profile Settings')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
        {{-- <div style="width: 40px; height: 40px; background-color: var(--discord-primary); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
            <i class="fas fa-user-cog text-white"></i>
        </div> --}}
        <div>
            <h4 class="mb-0">{{ __('provider.my_profile') }}</h4>
            <p class="text-muted mb-0" style="font-size: 14px; color: var(--discord-light);">{{ __('provider.manage_personal_info') }}</p>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert-container" style="margin-bottom: 20px;">
    <div class="discord-alert discord-alert-success">
        <i class="fas fa-check-circle me-2"></i>
        <span>{{ session('success') }}</span>
        <button type="button" class="close-btn" onclick="this.parentElement.style.display='none';">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

<div class="row g-4">
    <!-- Profile Information -->
    <div class="col-lg-6">
        <div class="discord-card mb-4">
            <div class="discord-card-header">
                <i class="fas fa-user me-2" style="color: var(--discord-primary);"></i>
                {{ __('provider.profile_information') }}
            </div>
            <div class="p-4">
                <form action="{{ route('provider.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="text-center mb-4" style="background-color: var(--discord-darkest); border-radius: 8px; padding: 20px;">
                        <div class="profile-image-container mx-auto" style="width: 100px; height: 100px; overflow: hidden; border-radius: 50%; border: 4px solid var(--discord-primary); margin-bottom: 16px;">
                            @if($user->profile_image)
                                <img src="@userProfileImage($user->profile_image)" alt="{{ $user->name }}" class="img-fluid" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <div style="width: 100%; height: 100%; background-color: var(--discord-primary); display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-user fa-2x" style="color: white;"></i>
                                </div>
                            @endif
                        </div>
                        <h5 class="mb-1" style="color: var(--discord-lightest);">{{ $user->name }}</h5>
                        <p style="color: var(--discord-light); font-size: 14px;">{{ $user->email }}</p>
                        <div class="mt-3">
                            <label for="profile_image" class="discord-btn discord-btn-sm">
                                <i class="fas fa-camera me-2"></i> {{ __('provider.change_photo') }}
                            </label>
                            <input type="file" id="profile_image" name="profile_image" class="d-none" accept="image/*">
                            <div id="file-name" style="font-size: 12px; color: var(--discord-light); margin-top: 8px;"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="name" style="display: block; margin-bottom: 8px; color: var(--discord-lightest); font-weight: 500;">
                            {{ __('provider.full_name') }}
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                            style="background-color: var(--discord-dark); border: none; color: var(--discord-lightest); padding: 10px 12px; border-radius: 4px; width: 100%;">
                        @error('name')
                            <div style="color: var(--discord-red); font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" style="display: block; margin-bottom: 8px; color: var(--discord-lightest); font-weight: 500;">
                            {{ __('provider.email_address') }}
                        </label>
                        <div style="position: relative;">
                            <i class="fas fa-envelope" style="position: absolute; left: 12px; top: 11px; color: var(--discord-light);"></i>
                            <input type="email" id="email" name="email"  value="{{ old('email', $user->email) }}" required
                                style="background-color: var(--discord-dark); border: none; color: var(--discord-lightest); padding: 10px 12px 10px 35px; border-radius: 4px; width: 100%; text-align: left; ">
                        </div>
                        @error('email')
                            <div style="color: var(--discord-red); font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="phone" style="display: block; margin-bottom: 8px; color: var(--discord-lightest); font-weight: 500;">
                            {{ __('provider.phone_number') }}
                        </label>
                        <div style="position: relative;">
                            <i class="fas fa-phone" style="position: absolute; left: 12px; top: 11px; color: var(--discord-light);"></i>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                                style="background-color: var(--discord-dark); border: none; color: var(--discord-lightest); padding: 10px 12px 10px 35px; border-radius: 4px; width: 100%; text-align: left;">
                        </div>
                        @error('phone')
                            <div style="color: var(--discord-red); font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="discord-btn" style="width: 100%;">
                        <i class="fas fa-save me-2"></i> {{ __('provider.save_changes') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Change Password -->
    <div class="col-lg-6">
        <div class="discord-card mb-4">
            <div class="discord-card-header">
                 <i class="fas fa-lock me-2" style="color: var(--discord-primary);"></i>
                 {{ __('provider.change_password') }}
             </div>
            <div class="p-4">
                <form action="{{ route('provider.profile.update-password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="current_password" style="display: block; margin-bottom: 8px; color: var(--discord-lightest); font-weight: 500;">
                             {{ __('provider.current_password') }}
                         </label>
                        <div style="position: relative;">
                            <i class="fas fa-key" style="position: absolute; left: 12px; top: 11px; color: var(--discord-light);"></i>
                            <input type="password" id="current_password" name="current_password" required
                                style="background-color: var(--discord-dark); border: none; color: var(--discord-lightest); padding: 10px 12px 10px 35px; border-radius: 4px; width: 100%; text-align: left;">
                        </div>
                        @error('current_password')
                            <div style="color: var(--discord-red); font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="new_password" style="display: block; margin-bottom: 8px; color: var(--discord-lightest); font-weight: 500;">
                             {{ __('provider.new_password') }}
                         </label>
                        <div style="position: relative;">
                            <i class="fas fa-lock" style="position: absolute; left: 12px; top: 11px; color: var(--discord-light);"></i>
                            <input type="password" id="password" name="password" required
                                style="background-color: var(--discord-dark); border: none; color: var(--discord-lightest); padding: 10px 12px 10px 35px; border-radius: 4px; width: 100%; text-align: left;">
                        </div>
                        @error('password')
                            <div style="color: var(--discord-red); font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="new_password_confirmation" style="display: block; margin-bottom: 8px; color: var(--discord-lightest); font-weight: 500;">
                             {{ __('provider.confirm_new_password') }}
                         </label>
                        <div style="position: relative;">
                            <i class="fas fa-lock" style="position: absolute; left: 12px; top: 11px; color: var(--discord-light);"></i>
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                style="background-color: var(--discord-dark); border: none; color: var(--discord-lightest); padding: 10px 12px 10px 35px; border-radius: 4px; width: 100%; text-align: left;">
                        </div>
                    </div>

                    <button type="submit" class="discord-btn" style="width: 100%;">
                        <i class="fas fa-key me-2"></i> {{ __('provider.update_password') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Store Information -->
        <div class="discord-card mb-4">
            <div class="discord-card-header">
                <i class="fas fa-store me-2" style="color: var(--discord-primary);"></i>
                {{ __('provider.store_settings') }}
            </div>
            <div class="p-4">
                <div style="background-color: var(--discord-darkest); border-radius: 8px; padding: 16px; margin-bottom: 16px;">
                    <h5 style="font-size: 16px; font-weight: 600; color: var(--discord-lightest); margin-bottom: 10px;">
                        {{ __('provider.your_store') }}
                    </h5>
                    <p style="color: var(--discord-light); font-size: 14px; margin-bottom: 15px;">
                        {{ __('provider.customize_store_description') }}
                    </p>
                    <div class="d-flex gap-2">
                        <a href="/provider/settings" class="discord-btn">
                            <i class="fas fa-cog me-2"></i> {{ __('provider.store_settings') }}
                        </a>
                    </div>
                </div>

                <div style="background-color: var(--discord-darkest); border-radius: 8px; padding: 16px;">
                    <h5 style="font-size: 16px; font-weight: 600; color: var(--discord-lightest); margin-bottom: 10px;">
                        {{ __('provider.quick_links') }}
                    </h5>
                    <div class="quick-links" style="display: grid; grid-template-columns: 1fr 1fr;  gap: 10px;">
                        <a href="{{ route('provider.provider-products.index') }}" class="d-flex align-items-center" style="text-decoration: none; color: var(--discord-lightest); background-color: var(--discord-dark); gap: 10px; padding: 10px; border-radius: 6px; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='var(--discord-dark-hover)'" onmouseout="this.style.backgroundColor='var(--discord-dark)'">
                            <i class="fas fa-box me-2" style="color: var(--discord-primary);"></i> {{ __('provider.my_products') }}
                        </a>
                        <a href="{{ route('provider.orders.index') }}" class="d-flex align-items-center" style="text-decoration: none; color: var(--discord-lightest); gap: 10px; background-color: var(--discord-dark); padding: 10px; border-radius: 6px; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='var(--discord-dark-hover)'" onmouseout="this.style.backgroundColor='var(--discord-dark)'">
                            <i class="fas fa-shopping-cart me-2" style="color: var(--discord-primary);"></i> {{ __('provider.my_orders') }}
                        </a>
                        <a href="#" class="d-flex align-items-center" style="text-decoration: none; color: var(--discord-lightest); background-color: var(--discord-dark); gap:10px; padding: 10px; border-radius: 6px; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='var(--discord-dark-hover)'" onmouseout="this.style.backgroundColor='var(--discord-dark)'">
                            <i class="fas fa-credit-card me-2" style="color: var(--discord-primary);"></i> {{ __('provider.payments') }}
                        </a>
                        <a href="#" class="d-flex align-items-center" style="text-decoration: none; color: var(--discord-lightest); background-color: var(--discord-dark); gap:10px; padding: 10px; border-radius: 6px; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='var(--discord-dark-hover)'" onmouseout="this.style.backgroundColor='var(--discord-dark)'">
                            <i class="fas fa-chart-line me-2" style="color: var(--discord-primary);"></i> {{ __('provider.analytics') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

<style>
/* RTL Support for Arabic */
[dir="rtl"] .discord-card-header {
    text-align: right;
}

[dir="rtl"] .form-label {
    text-align: right;
}

[dir="rtl"] .discord-btn {
    text-align: center;
}

[dir="rtl"] .quick-links a {
    text-align: right;
    flex-direction: row-reverse;
}

[dir="rtl"] .quick-links a i {
    margin-left: 8px;
    margin-right: 0;
}

[dir="rtl"] .discord-card-header i {
    margin-left: 8px;
    margin-right: 0;
}

[dir="rtl"] .discord-btn i {
    margin-left: 8px;
    margin-right: 0;
}

[dir="rtl"] input[type="text"],
[dir="rtl"] input[type="email"],
[dir="rtl"] input[type="tel"],
[dir="rtl"] input[type="password"] {
    text-align: right;
    padding-right: 40px;
    padding-left: 12px;
}

[dir="rtl"] .fas {
    right: 12px;
    left: auto;
}

[dir="rtl"] .me-2 {
    margin-left: 0.5rem !important;
    margin-right: 0 !important;
}

[dir="rtl"] .profile-avatar {
    margin-left: 16px;
    margin-right: 0;
}

[dir="rtl"] .d-flex {
    flex-direction: row-reverse;
}

[dir="rtl"] .gap-2 > * + * {
    margin-right: 0.5rem;
    margin-left: 0;
}
</style>

@section('scripts')
<script>
    $(document).ready(function() {
        // Show selected filename when uploading profile image
        $('#profile_image').change(function() {
            var fileName = $(this).val().split('\\').pop();
            $('#file-name').text(fileName ? fileName : '');

            // Preview image before upload
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
