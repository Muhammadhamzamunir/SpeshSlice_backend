<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class customizeCake extends Model
{
    
    use HasFactory;
    protected $table = 'customize_cakes';
    protected $fillable = ['bakery_id','name','image_url', 'price','quantity'];

    public function bakery()
    {
        return $this->belongsTo(Bakery::class);
    }
   
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
