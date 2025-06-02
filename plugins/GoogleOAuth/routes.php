<?php

use Illuminate\Support\Facades\Route;
use Plugins\GoogleOAuth\Controllers\GoogleOAuthController;

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('admin/plugin/google_oauth/settings', [GoogleOAuthController::class, 'googleOAuthSettings'])->name('admin.plugin.google_oauth.settings');
    Route::post('admin/plugin/google_oauth/settings/update', [GoogleOAuthController::class, 'googleOAuthSettingsUpdate'])->name('admin.google_oauth_settings.update')->middleware('demo.mode');
});
