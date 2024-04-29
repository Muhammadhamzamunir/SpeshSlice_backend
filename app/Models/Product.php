<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Review;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'bakery_id',
        'image_url',
        'category',
        'no_of_pounds',
        'no_of_serving',
        'quantity',
        'is_available',
        'rating',
        'reviews_count'
    ];

    public function discounts()
    {
        return $this->hasMany(ProductDiscount::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }
    public function bakery()
    {
        return $this->belongsTo(Bakery::class);
    }
    public function category()
{
    return $this->belongsTo(Category::class, 'category', 'id');
}

}
