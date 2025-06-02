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
        Schema::create('custom_domain_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->string('custom_domain_request_id')->uniqid();
            $table->string('user_id');
            $table->string('card_id');
            $table->string('previous_domain');
            $table->string('current_domain');
            $table->integer('transfer_status')->default(0);
            $table->string('status')->default(1);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_domain_requests');
    } 
};
