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
        Schema::create('nfc_card_keys', function (Blueprint $table) {
            $table->increments('id')->uniqid();
            $table->string('nfc_card_key_id');
            $table->string('unqiue_key');
            $table->string('key_type');
            $table->string('card_id')->nullable();
            $table->enum('link_status', ['linked', 'unlinked'])->default('unlinked');
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
        Schema::dropIfExists('nfc_card_keys');
    }
};
