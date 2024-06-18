<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\User;
use App\Models\CustomizeCake;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{




    public function deleteProductFromCart($id){
        $item =Cart::find($id);
        if($item){
            $item->delete();
            return response()->json(["success"=>"Item Removed"], 200);
        }else{
            return response()->json(["error"=>"Item Not Found"], 200);

        }
    }

public function getCartProducts($user_id)
{
    $cartItems = Cart::where('user_id', $user_id)->get();       
    $regularProducts = [];
    $customizeProducts = [];
    foreach ($cartItems as $cartItem) {
        if ($cartItem->customize) {
            $customizeCake = CustomizeCake::find($cartItem->product_id);
            if ($customizeCake) {
                
                $customizeProducts[] = [
                    'id' => $customizeCake->id,
                    'name' => $customizeCake->name,
                    'image_url' => $customizeCake->image_url,
                    'price' => $customizeCake->price,
                    'cart_quantity' => $cartItem->quantity,
                    'cart_id' => $cartItem->id,
                    'bakery_id'=>$customizeCake->bakery_id,
                    'customize' => $cartItem->customize,
                    "discounts"=> []
                ];
            }
        } else {
            $product = Product::with('discounts')->where('id', $cartItem->product_id)->first();
            if ($product) {
                $product->cart_quantity = $cartItem->quantity;
                $product->cart_id = $cartItem->id;
                $product->customize =false;
                $regularProducts[] = $product;
            }
        }
    }
    $mergedProducts = array_merge($regularProducts, $customizeProducts);

    return response()->json(["data" => $mergedProducts]);
}

public function addCartProducts(Request $request, $user_id, $product_id, $quantity)
{
    $customize = $request->input('customize', false); 
  
    if (!$customize) {
     
        $product = Product::find($product_id);
        
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }
        
        $cartItem = Cart::where('user_id', $user_id)
                        ->where('product_id', $product_id)
                        ->first();

        if ($cartItem) {
      
            if (($cartItem->quantity + $quantity) > $product->quantity) {
                return response()->json(['error' => 'Quantity exceeds available stock in cart'], 400);
            }
            
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
           
            if ($quantity > $product->quantity) {
                return response()->json(['error' => 'Quantity exceeds available stock'], 400);
            }
            
            Cart::create([
                'user_id' => $user_id,
                'product_id' => $product_id,
                'quantity' => $quantity,
            ]);
        }

        return response()->json(['success' => 'Product added to cart successfully']);
    } else {
   
        Cart::create([
            'user_id' => $user_id,
            'product_id' => $product_id,
            'quantity' => $quantity,
            'customize' => true, 
        ]);

        return response()->json(['success' => 'Product with customization added to cart successfully']);
    }
}


// public function addCartProducts(Request $request,$user_id, $product_id, $quantity)
// {
//     $customize = $request->input('customize');
//     if(!$customize){
//     $product = Product::find($product_id);
    
//     if (!$product) {
//         return response()->json(['error' => 'Product not found'], 404);
//     }

//     $cartItem = Cart::where('user_id', $user_id)
//                     ->where('product_id', $product_id)
//                     ->first();

//     if ($cartItem) {
       
//         if (($cartItem->quantity + $quantity) > $product->quantity) {
//             return response()->json(['error' => 'Check You Cart!..Quantity exceeds available stock'], 400);
//         }
        
//         $cartItem->quantity += $quantity;
//         $cartItem->save();
//     } else {
       
//         if ($quantity > $product->quantity) {
//             return response()->json(['error' => 'Check You Cart!..Quantity exceeds available stock'], 400);
//         }
        
//         Cart::create([
//             'user_id' => $user_id,
//             'product_id' => $product_id,
//             'quantity' => $quantity,
//         ]);
//     }

//     return response()->json(['success' => 'Product added to cart successfully']);

// }else{
//     Cart::create([
//         'user_id' => $user_id,
//         'product_id' => $product_id,
//         'quantity' => $quantity,
//          ' $customize'=>true
//     ]);
// }
// }
}
