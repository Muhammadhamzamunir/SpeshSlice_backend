<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomizeToCartTable extends Migration
{
    public function up()
    {
        Schema::table('cart', function (Blueprint $table) {
            $table->boolean('customize')->default(false);
        });
    }

    public function down()
    {
        Schema::table('cart', function (Blueprint $table) {
            $table->dropColumn('customize');
        });
    }
}
