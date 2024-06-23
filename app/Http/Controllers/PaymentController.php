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
use App\Models\Order;
use App\Models\CustomizeCake;
use Illuminate\Support\Str;
class PaymentController extends Controller
{
    public function getUserOrders(Request $request, $id) {
    
        $ordersWithoutCustomName = Order::with('product', 'bakery')
            ->where('user_id', $id)
            ->whereNull('custom_Name')
            ->get();
    
        $ordersWithCustomName = Order::with('customizeCake', 'bakery') 
            ->whereNotNull('custom_Name')
            ->get();
    
        
        $ordersWithCustomName->transform(function ($order) {
            $order->product = $order->customizeCake;
            unset($order->customizeCake);
            return $order;
        });
    
        $userOrders = $ordersWithoutCustomName->merge($ordersWithCustomName);
    
        return response()->json($userOrders, 200);
    }
    

    public function getBakeryOrders(Request $request,$id){
        $ordersWithoutCustomName = Order::with('product', 'bakery')
        ->where('user_id', $id)
        ->whereNull('custom_Name')
        ->get();

        $ordersWithCustomName = Order::with('customizeCake', 'bakery') 
            ->whereNotNull('custom_Name')
            ->get();

        
        $ordersWithCustomName->transform(function ($order) {
            $order->product = $order->customizeCake;
            unset($order->customizeCake);
            return $order;
    });

        $bakeryOrders = $ordersWithoutCustomName->merge($ordersWithCustomName);
            return response()->json($bakeryOrders, 200);
    }
    public function cancelOrder(Request $request, $id)
{
    
    $order = Order::find($id);
    if (!$order) {
        return response()->json(["error" => "Order not found"], 404);
    }

    $order->delete();

    return response()->json(["success" => "Order removed successfully"], 200);
}

public function updateStatus(Request $request, $id)
{    
    $request->validate([
        'status' => 'required|in:pending,Baking,completed,cancelled',
    ]);
    $order = Order::find($id);
    if (!$order) {
        return response()->json(['error' => 'Order not found'], 404);
    }
    $order->status = $request->input('status');
    $order->save();
    return response()->json(['success' => 'Order status updated successfully'], 200);
}

    public function processPayment(Request $request)
    {
    // return response()->json($request->all(), 200,);
        $method = $request->input('method');
        $totalAmount = $request->input('totalAmount');
        $userEmail = $request->input('userEmail');
        $user_id = $request->input('userId');
        $cart_product = $request->input('cartItems'); 
        $selectedAddress = $request->input('selectedAddress');
        $userPhone = $request->input('userPhone');
        $orderId =Str::uuid();
        if($method==='CashOnDelivery'){
            $cartItems = Cart::where('user_id',$user_id)->get();
            $cartItems->each->delete();
           
            foreach ($cart_product as $cartItem) {
                $customize = $cartItem['customize'];
                if($customize=='false'){ 
               
                $product = Product::find($cartItem['id']);
                if ($product) {
                    Order::create([
                        'orderId' => $orderId,
                        'user_id' => $user_id,
                        'bakery_id' => $cartItem['bakery_id'],
                        'product_id' => $cartItem['id'],
                        'total_amount' => $totalAmount,
                        'unit_price' => $cartItem['unit_price'] * $cartItem['quantity'],
                        'selected_address' => $selectedAddress,
                        'user_phone' => $userPhone,
                        'method' => $method,
                        'quantity' => $cartItem['quantity'],
                        
                    ]);
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
            else{
                
                    Order::create([
                        'orderId' => $orderId,
                        'user_id' => $user_id,
                        'product_id' => $cartItem['id'],
                        'bakery_id' => $cartItem['bakery_id'],
                        'total_amount' => $totalAmount,
                        'unit_price' => $cartItem['unit_price'] * $cartItem['quantity'],
                        'selected_address' => $selectedAddress,
                        'user_phone' => $userPhone,
                        'method' => $method,
                        'quantity' => $cartItem['quantity'],
                        'custom_Name'=>$cartItem['customize']
                        
                        
                    ]);
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
                $customize = $cartItem['customize'];
                if($customize){
                $product = Product::find($cartItem['id']);
                if ($product) {
                    
                    Order::create([
                        'orderId' => $orderId,
                        'user_id' => $user_id,
                        'bakery_id' => $cartItem['bakery_id'],
                        'product_id' => $cartItem['id'],
                        'total_amount' => $totalAmount,
                        'unit_price' => $cartItem['unit_price'] * $cartItem['quantity'],
                        'selected_address' => $selectedAddress,
                        'user_phone' => $userPhone,
                        'method' => $method,
                        'quantity' => $cartItem['quantity'],
                        'transaction_id'=>$paymentIntent->id
                        
                    ]);
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
            else{
                    Order::create([
                        'orderId' => $orderId,
                        'user_id' => $user_id,
                        'product_id' => $cartItem['id'],
                        'total_amount' => $totalAmount,
                        'unit_price' => $cartItem['unit_price'] * $cartItem['quantity'],
                        'selected_address' => $selectedAddress,
                        'user_phone' => $userPhone,
                        'bakery_id' => $cartItem['bakery_id'],
                        'method' => $method,
                        'quantity' => $cartItem['quantity'],
                        'custom_Name'=>$cartItem['customize']
                     
                        
                    ]);
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
