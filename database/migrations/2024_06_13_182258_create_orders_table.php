<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('orderId');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('bakery_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->decimal('unit_price', 10, 2);
            $table->string('selected_address');
            $table->string('user_phone');
            $table->string('method');
            $table->string('transaction_id')->nullable();
            $table->enum('status', ['pending','Baking', 'completed', 'cancelled'])->default('pending');
            $table->integer('quantity');
            $table->string('custom_Name')->nullable();
            $table->string('img_url')->nullable();
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
        Schema::dropIfExists('orders');
    }
}
