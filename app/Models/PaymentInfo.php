<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentInfo extends Model
{
    use HasFactory;
    protected $table = 'payment_info';
     protected $fillable =['bakery_id','payment'];
     public function bakery()
     {
         return $this->belongsTo(Bakery::class);
     }
    
}
