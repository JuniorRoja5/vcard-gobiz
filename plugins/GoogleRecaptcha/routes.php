<?php

use Illuminate\Support\Facades\Route;
use Plugins\GoogleRecaptcha\Controllers\GoogleRecaptchaController;

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('admin/plugin/google_recaptcha/settings', [GoogleRecaptchaController::class, 'googleRecaptchaSettings'])->name('admin.plugin.google_recaptcha.settings');
    Route::post('admin/plugin/google_recaptcha/settings/update', [GoogleRecaptchaController::class, 'googleRecaptchaSettingsUpdate'])->name('admin.google_recaptcha_settings.update')->middleware('demo.mode');
});
