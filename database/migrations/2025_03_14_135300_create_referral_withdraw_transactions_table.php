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
        Schema::create('referral_withdraw_transactions', function (Blueprint $table) {
            $table->increments('id')->uniqid();
            $table->string('referral_withdraw_request_id');
            $table->string('transfer_id');
            $table->text('notes')->nullable();
            $table->integer('payment_status')->default(0);
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
        Schema::dropIfExists('referral_withdraw_transactions');
    }
};
