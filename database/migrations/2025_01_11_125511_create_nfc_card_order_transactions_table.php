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
        Schema::create('nfc_card_order_transactions', function (Blueprint $table) {
            $table->increments('id')->uniqid();
            $table->string('nfc_card_order_transaction_id');
            $table->string('nfc_card_order_id');
            $table->string('payment_transaction_id');
            $table->string('payment_method'); 
            $table->string('currency');
            $table->decimal('amount', 15, 2);
            $table->string('invoice_number')->nullable();
            $table->string('invoice_prefix')->nullable();
            $table->text('invoice_details');
            $table->enum('payment_status', ['pending','success','failed'])->default('pending');
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
        Schema::dropIfExists('nfc_card_order_transactions');
    }
};
