<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $table = 'addresses';

    protected $fillable = ['entity_id','default','country', 'city', 'street', 'longitude', 'latitude'];


    public function bakery()
    {
        return $this->belongsTo(Bakery::class, 'entity_id');
    }
    
}
