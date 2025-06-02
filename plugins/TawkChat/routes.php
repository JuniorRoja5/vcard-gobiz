<?php

use Illuminate\Support\Facades\Route;
use Plugins\TawkChat\Controllers\TawkChatController;

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('admin/plugin/tawkchat/settings', [TawkChatController::class, 'tawkChatSettings'])->name('admin.plugin.tawkchat.settings');
    Route::post('admin/plugin/tawkchat/settings/update', [TawkChatController::class, 'tawkChatSettingsUpdate'])->name('admin.tawkchat_settings.update')->middleware('demo.mode');
});
