<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bakery extends Model
{
    use HasFactory;
   
    protected $table = 'bakeries';

    protected $fillable = ['owner_name','logo_url','user_id', 'business_name', 'specialty', 'timing', 'email', 'phone', 'price_per_pound', 'price_per_decoration', 'price_per_tier', 'price_for_shape', 'tax', 'address_id','description','disabled'];

    public function address()
    {
        return $this->hasOne(Address::class, 'entity_id');
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function customizeproduct()
    {
        return $this->hasMany(CustomizeCake::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function reviews()
    {
        return $this->hasMany(BakeryReview::class);
    }
    public function PaymentInfo()
    {
        return $this->belongsTo(PaymentInfo::class);
    }

}
