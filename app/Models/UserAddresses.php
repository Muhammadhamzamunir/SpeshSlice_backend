<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddresses extends Model
{
    use HasFactory;

    protected $table = 'userAddresses';

    protected $fillable = ['user_id','default','country', 'city', 'street', 'longitude', 'latitude'];


    public function bakery()
    {
        return $this->belongsTo(User::class,);}
}
