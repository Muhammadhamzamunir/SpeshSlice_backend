<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\Token;
use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Feedback;
use App\Models\Bakery;

class PaymentController extends Controller
{
    public function processPayment(Request $request)
    {

        $method = $request->input('method');
        $totalAmount = $request->input('totalAmount');
        $userEmail = $request->input('userEmail');
        $user_id = $request->input('userId');
        $cart_product = $request->input('cartItems'); 
        if($method==='CashOnDelivery'){
            $cartItems = Cart::where('user_id',$user_id)->get();
            $cartItems->each->delete();
           
            foreach ($cart_product as $cartItem) {
                $product = Product::find($cartItem['id']);
                if ($product) {
                    
                    $existingFeedback = Feedback::where('user_id', $user_id)
                        ->where('product_id', $product->id)
                        ->first();
            
                  
                    if (!$existingFeedback) {
                        Feedback::create([
                            'user_id' => $user_id,
                            'product_id' => $product->id,
                            'bakery_id' => $product->bakery_id,
                            'Is_bakery' => 0 
                        ]);
                        Feedback::create([
                            'user_id' => $user_id,
                            'product_id' => $product->id,
                            'bakery_id' => $product->bakery_id,
                            'Is_bakery' => 1 
                        ]);
                    }
                    $product->quantity -= $cartItem['quantity']; 
                    
                    if ($product->quantity === 0) {
                        $product->is_available = false;
                    } 
                    $product->save();
                }
            }


            return response()->json(["success"=>"Order Placed"], 200,);  
        }
        
        else{

        
            $paymentMethod = $request->input('payment');
           Stripe::setApiKey(env('STRIPE_SECRET'));

      
       

        try {
           
            $paymentMethodId = $paymentMethod['id'];
            $paymentIntent = PaymentIntent::create([
                'amount' => $totalAmount * 100, 
                'currency' => 'usd',
                'payment_method' => $paymentMethodId,
                'confirm' => true, 
                'confirmation_method' => 'automatic',
                'description' => 'Payment for items',
                'receipt_email' => $userEmail,
                'return_url'=>'https://hamzamunir.netlify.app'
            ]);

            
            if ($paymentIntent->status === 'succeeded') {
                $cartItems = Cart::where('user_id',$user_id)->get();
            $cartItems->each->delete();
           
            foreach ($cart_product as $cartItem) {
                $product = Product::find($cartItem['id']);
                if ($product) {
                    
                    $existingFeedback = Feedback::where('user_id', $user_id)
                        ->where('product_id', $product->id)
                        ->first();
            
                  
                    if (!$existingFeedback) {
                        Feedback::create([
                            'user_id' => $user_id,
                            'product_id' => $product->id,
                            'bakery_id' => $product->bakery_id,
                            'Is_bakery' => 0 
                        ]);
                        Feedback::create([
                            'user_id' => $user_id,
                            'product_id' => $product->id,
                            'bakery_id' => $product->bakery_id,
                            'Is_bakery' => 1 
                        ]);
                    }
                    $product->quantity -= $cartItem['quantity'];
                    if ($product->quantity === 0) {
                        $product->is_available = false;
                    } 
                    $product->save();
                }
            }


            return response()->json(["success"=>"Order Placed"], 200,);  
            } else {
                return response()->json(['error' => 'Payment failed']);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }}
    }
}
