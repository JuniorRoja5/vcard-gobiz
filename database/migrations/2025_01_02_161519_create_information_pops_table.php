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
        Schema::create('information_pops', function (Blueprint $table) {
            $table->increments('id')->uniqid();
            $table->string('information_pop_id');
            $table->string('card_id');
            $table->boolean('confetti_effect')->default(0);
            $table->longText('info_pop_image');
            $table->string('info_pop_title');
            $table->text('info_pop_desc');
            $table->string('info_pop_button_text');
            $table->longText('info_pop_button_url');
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
        Schema::dropIfExists('information_pops');
    }
};
