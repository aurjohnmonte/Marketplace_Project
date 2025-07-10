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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->unsignedBigInteger('from_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('review_type', 20);
            $table->string('comment', 250)->nullable();
            $table->integer('rate')->nullable();
            $table->string('to', 10);
            
            $table->foreign('shop_id')->references('id')->on('shops');
            $table->foreign('from_id')->references('id')->on('users');
            $table->foreign('product_id')->references('id')->on('products');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
