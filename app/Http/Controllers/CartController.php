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
//     public function getCartProducts($user_id)
// {
//     // Retrieve cart items for the user
//     $items = Cart::where('user_id', $user_id)->get();       

//     // Collect product IDs from the cart items
//     $productIds = $items->pluck('product_id')->toArray();

//     // Retrieve products with discounts where their IDs match the IDs in the cart
//     $products = Product::with('discounts')->whereIn('id', $productIds)->get();

//     // Modify the products array to include the id attribute for each item
//     $modifiedProducts = $products->map(function ($product) use ($items) {
//         // Find the corresponding cart item for the product
//         $cartItem = $items->where('product_id', $product->id)->first();

//         // Add the id attribute to the product
//         $product->setAttribute('cart_id', $cartItem->id);
//         $product->setAttribute('cart_quantity', $cartItem->quantity);

//         return $product;
//     });

//     return response()->json(["data" => $modifiedProducts]);
// }
public function getCartProducts($user_id)
{
    // Retrieve cart items for the user
    $cartItems = Cart::where('user_id', $user_id)->get();       

    // Collect product IDs from the cart items
    $productIds = $cartItems->pluck('product_id')->toArray();

    // Initialize arrays to collect regular products and customize products
    $regularProducts = [];
    $customizeProducts = [];

    // Iterate through each cart item to categorize products
    foreach ($cartItems as $cartItem) {
        // Fetch the corresponding product
        $product = Product::find($cartItem->product_id);

        if (!$product) {
            // Handle case where product is not found (optional)
            continue;
        }

        // Check if the product is a customizeCake product
        if ($cartItem->customize) {
            // Fetch details from customizeCake table
            $customizeCake = CustomizeCake::find($cartItem->product_id);

            if ($customizeCake) {
                // Add customizeCake product to customizeProducts array
                $customizeProducts[] = [
                    'id' => $customizeCake->id,
                    'name' => $customizeCake->name,
                    'image_url' => $customizeCake->image_url,
                    'price' => $customizeCake->price,
                    'quantity' => $cartItem->quantity,
                    'cart_id' => $cartItem->id,
                ];
            }
        } else {
            // Add regular product to regularProducts array
            $regularProducts[] = [
                'id' => $product->id,
                'name' => $product->name,
                'image_url' => $product->image_url,
                'price' => $product->price,
                'quantity' => $cartItem->quantity,
                'cart_id' => $cartItem->id,
            ];
        }
    }

    // Merge regular and customize products
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
