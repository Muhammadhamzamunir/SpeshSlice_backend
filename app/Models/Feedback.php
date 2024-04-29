<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;
    protected $table = 'feedback_notification';

    protected $fillable = ['user_id','product_id','bakery_id', 'Is_bakery'];


}
