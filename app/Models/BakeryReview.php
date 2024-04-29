<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BakeryReview extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'bakery_id',
        'description',
        'rating',
    ];

    public function bakery()
    {
        return $this->belongsTo(Bakery::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
