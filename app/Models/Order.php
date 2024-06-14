<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'orderId',
        'user_id',
        'bakery_id',
        'product_id',
        'total_amount',
        'unit_price',
        'selected_address',
        'user_phone',
        'method',
        'status',
        'quantity',
        'custom_name',
        'img_url',
        'transaction_id'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bakery()
    {
        return $this->belongsTo(Bakery::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
