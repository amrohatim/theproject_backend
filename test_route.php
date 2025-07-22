<?php

// Temporary test route for vendor login
Route::get('/test-vendor-login', function () {
    $vendor = \App\Models\User::find(96);
    if ($vendor) {
        \Illuminate\Support\Facades\Auth::login($vendor);
        return redirect('/vendor/products/60/edit')->with('success', 'Logged in as ' . $vendor->name);
    }
    return 'Vendor not found';
});
