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
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('name', 50);
            $table->string('address', 200);
            $table->string('description', 200)->nullable();
            $table->text('category');
            $table->string('profile_photo', 200);
            $table->string('cover_photo', 200);
            $table->integer('latitude');
            $table->integer('longitude');
            $table->integer('total_views');
            $table->integer('overall_rate');

            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
