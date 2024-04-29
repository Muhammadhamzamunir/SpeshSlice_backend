<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ProductReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'bakery_id',
        'description',
        'rating',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        // Define the belongs to relationship
        $belongsTo = $this->belongsTo(User::class, 'user_id');

        // Set the key type for the parent model to string
        $belongsTo->getParent()->setKeyType('string');

        // Return the relationship
        return $belongsTo;
    }

    public function bakery()
    {
        return $this->belongsTo(Bakery::class);
    }
}
