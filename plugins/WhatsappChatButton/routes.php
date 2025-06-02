<?php

use Illuminate\Support\Facades\Route;
use Plugins\WhatsappChatButton\Controllers\WhatsappChatButtonController;

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('admin/plugin/whatsapp_chat_button/settings', [WhatsappChatButtonController::class, 'whatsappChatButtonSettings'])->name('admin.plugin.whatsapp_chat_button.settings');
    Route::post('admin/plugin/whatsapp_chat_button/settings/update', [WhatsappChatButtonController::class, 'whatsappChatButtonSettingsUpdate'])->name('admin.whatsapp_chat_button_settings.update')->middleware('demo.mode');
});
