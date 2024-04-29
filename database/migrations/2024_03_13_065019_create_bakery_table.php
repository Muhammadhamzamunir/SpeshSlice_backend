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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('entity_id');
            $table->string('country');
            $table->string('city');
            $table->string('street');
            $table->string('longitude');
            $table->string('latitude');
            $table->boolean('default')->default(false); 
            $table->timestamps();
        });

        Schema::create('bakeries', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('user_id');
            $table->string('owner_name');
            $table->string('business_name');
            $table->string('specialty');
            $table->string('timing');
            $table->string('email')->unique();
            $table->string('phone');
            $table->text('description');
            $table->string('logo_url');
            $table->string('price_per_pound');
            $table->string('price_per_decoration');
            $table->string('price_per_tier');
            $table->string('price_for_shape');
            $table->string('tax');
            $table->decimal('averageRating', 3, 1)->default(3.4); 
            $table->integer('rating_count')->default(1);
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bakeries');
        Schema::dropIfExists('addresses');
    }
};
