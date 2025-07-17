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
        Schema::create('pendingaccounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role', 20);
            $table->string('firstname', 50);
            $table->string('m_initial', 1)->nullable();
            $table->string('lastname', 50);
            $table->string('gender', 10);
            $table->date('birthday');
            $table->string('contact_no', 11);
            $table->string('current_address', 100);
            $table->integer('total_followers')->default(0);
            $table->integer('age');
            $table->string('profile', 200);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendingaccounts');
    }
};
