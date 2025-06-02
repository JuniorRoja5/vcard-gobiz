<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nfc_card_designs', function (Blueprint $table) {
            $table->increments('id')->uniqid();
            $table->string('nfc_card_id'); 
            $table->string('nfc_card_name');
            $table->text('nfc_card_description')->nullable();
            $table->string('nfc_card_front_image')->nullable();
            $table->string('nfc_card_back_image')->nullable();
            $table->decimal('nfc_card_price', 8, 2);
            $table->integer('available_stocks')->default(0);
            $table->integer('status')->default(1);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nfc_card_designs');
    }
};
