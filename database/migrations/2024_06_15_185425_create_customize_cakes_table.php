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
        Schema::create('customize_cakes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bakery_id');
            $table->string('name')->default("Customize Cake");
            $table->string('image_url');
            $table->decimal('price', 10, 2);
            $table->integer('quantity')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customize_cakes');
    }
};
