<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->unsignedBigInteger('bakery_id');
            $table->string('image_url')->nullable();
            $table->unsignedBigInteger('category');
            $table->integer('no_of_pounds');
            $table->integer('no_of_serving');
            $table->integer('quantity')->default(0);
            $table->boolean('is_available')->default(true);
            $table->float('rating', 2, 1)->default(3); 
            $table->integer('reviews_count')->default(1);
            $table->timestamps();
        });

        Schema::create('product_discounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->decimal('discount_percentage', 5, 2)->default(0.0);
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->timestamps();
        });

        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('bakery_id');
            $table->text('description');
            $table->float('rating', 2, 1); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_reviews');
        Schema::dropIfExists('product_discounts');
        Schema::dropIfExists('products');
    }
}
