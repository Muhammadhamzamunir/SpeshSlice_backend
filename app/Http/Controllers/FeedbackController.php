<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Bakery;
use App\Models\Feedback;

class FeedbackController extends Controller
{
    public function getPendingFeedback($userId)
    {
       
        $userFeedback = Feedback::where('user_id', $userId)->get();
        
        $products = [];
        $bakeries = [];

       
        foreach ($userFeedback as $feedback) {
            if ($feedback->Is_bakery) {
                
                $bakery = Bakery::find($feedback->bakery_id);
                if ($bakery) {
                    $bakeries[$bakery->id] = $bakery;
                }
            } else {
                
                $product = Product::find($feedback->product_id);
                if ($product) {
                    $products[$product->id] = $product;
                }
            }
        }

       
        return response()->json([
            'products' => array_values($products),
            'bakeries' => array_values($bakeries)
        ], 200);
    }

    public function deletePendingFeedback($userId) {
        Feedback::where('user_id', $userId)->delete();
        return response()->json(["data",""], 200, $headers);
    }
    
}
