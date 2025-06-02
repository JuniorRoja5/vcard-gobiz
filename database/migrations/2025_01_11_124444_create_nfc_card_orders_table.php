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
        Schema::create('nfc_card_orders', function (Blueprint $table) {
            $table->increments('id')->uniqid();
            $table->string('nfc_card_order_id');
            $table->string('user_id');
            $table->string('nfc_card_id');
            $table->string('nfc_card_order_transaction_id');
            $table->text('nfc_card_logo')->nullable();
            $table->text('order_details');
            $table->text('delivery_address');
            $table->text('delivery_note');
            $table->enum('order_status', ['pending','processing','printing process begun','out for delivery','delivered','cancelled','hold','shipped'])->default('pending');
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
        Schema::dropIfExists('nfc_card_orders');
    }
};
