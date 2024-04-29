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
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Use Laravel's default id handling
            $table->string('username');
            $table->string('email')->unique();
            $table->string('phone');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('isBakeryRegistered')->default(false); 
            $table->string('profile_url')->default('https://firebasestorage.googleapis.com/v0/b/semester-project-10ee1.appspot.com/o/userProfile%2Favatar.webp?alt=media&token=7e6ee20b-fa86-4ec6-a4a5-a7c0a8afb311');
            $table->rememberToken();
            $table->timestamps();   
        });
        Schema::create('userAddresses', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('user_id');
            $table->string('country');
            $table->string('city');
            $table->string('street');
            $table->string('longitude');
            $table->string('latitude');
            $table->boolean('default')->default(false); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
