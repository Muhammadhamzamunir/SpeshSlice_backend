<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Feedback;
use App\Models\CustomizeCake;
use Illuminate\Validation\ValidationException;
use App\Models\ProductDiscount;
use Illuminate\Support\Facades\DB;
class ProductController extends Controller
{
   
    public function addCustomizeCake(Request $request)
    {
        
        $validatedData = $request->validate([
            'bakery_id' => 'required|integer',
            'name' => 'required|string',
            'image_url' => 'required|string',
            'price' => 'required|numeric',
            'quantity' => 'integer', 
        ]);

        
        $customizeCake = CustomizeCake::create([
            'bakery_id' => $validatedData['bakery_id'],
            'name' => $validatedData['name'],
            'image_url' => $validatedData['image_url'],
            'price' => $validatedData['price'],
            'quantity' => $validatedData['quantity'] ?? 0, 
        ]);

        return response()->json([
            'success' => 'Customize cake added successfully',
            'customize_cake' => $customizeCake->refresh()
        ], 200);
    }
    public function addProduct(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'bakery_id' => 'required',
                'image_url' => 'required|url',
                'category' => 'required',
                'no_of_pounds' => 'nullable|integer|min:1',
                'no_of_serving' => 'nullable|integer|min:1',
                'quantity' => 'required|integer|min:0',
                'is_available' => 'required',
                'discount_percentage' => 'nullable|numeric|min:0|max:100',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after:start_date',
               
            ]);
            
            // Create the product
            $product = Product::create($validatedData);
            // return response()->json(["success"=>"done"], 200);
    
            // If discount data is provided, create a discount for the product
            if ($validatedData['discount_percentage']!== null) {
                $discountData = [
                    'product_id' => $product->id,
                    'discount_percentage' => $validatedData['discount_percentage'],
                    'start_date' => $validatedData['start_date'],
                    'end_date' => $validatedData['end_date'],
                ];
    
                ProductDiscount::create($discountData);
            }
    
            return response()->json(['success' => 'Product added successfully', 'data' => $product], 200);
        } catch (ValidationException $e) {
            $errorMessages = implode(', ', $e->validator->errors()->all());
            return response()->json(['error' => $errorMessages], 400);
        }
    }
    

    public function deleteProduct($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }
    
        try {
            // Delete associated discounts
            $product->discounts()->delete();
    
            // Delete the product itself
            $product->delete();
    
            return response()->json(['success' => 'Product deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while deleting product and associated discounts'], 500);
        }
    }


    
    public function updateProduct(Request $request, $id)
    {

        // return response()->json(["id"=>$id,"data"=>$request], 200);
        try {
            $validatedData = $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
                'price' => 'sometimes|numeric|min:0',
                'image_url' => 'nullable|url',
                'category' => 'sometimes',
                'no_of_pounds' => 'sometimes|integer|min:1',
                'no_of_serving' => 'sometimes|integer|min:1',
                'quantity' => 'sometimes|integer|min:0',
                'is_available' => 'sometimes',
                'discount_percentage' => 'sometimes|numeric|min:0|max:100', // Added validation for discount_percentage
                'start_date' => 'sometimes|date',
                'end_date' => 'sometimes|date|after_or_equal:start_date',
            ]);
    
            
            $product = Product::find($id);
            if (!$product) {
                return response()->json(['error' => 'Product not found'], 404);
            }
    
            
            $product->update($validatedData);
    
            // Update the product's discount percentage if provided
            if ($request->has('discount_percentage')) {
                $product->discounts()->update(['discount_percentage' => $validatedData['discount_percentage']]);
            }
    
            // Update the product's discount start date and end date if provided
            if ($request->has('start_date') && $request->has('end_date')) {
                $product->discounts()->update([
                    'start_date' => $validatedData['start_date'],
                    'end_date' => $validatedData['end_date']
                ]);
            }
            $product->load('discounts');
    
            return response()->json(['success' => 'Product updated successfully', 'data' => $product], 200);
        } catch (ValidationException $e) {
            $errorMessages = implode(', ', $e->validator->errors()->all());
            return response()->json(['error' => $errorMessages], 400);
        } catch (\Exception $e) {
            Log::error('An error occurred while updating the product: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while updating the product',"errrr"=>$e->getMessage()], 500);
        }
    }
    


// public function getProduct(Request $request, $productId = null)
// {
    
//     try {
//         if (!$productId) {
           

//             $products = Product::with('discounts', 'bakery','reviews.user','category')->get();
//             return response()->json(['data' => $products], 200);
//         } else {
            
//             $product = Product::with('discounts', 'bakery', 'reviews.user','category')->find($productId);
//             if (!$product) {
//                 return response()->json(['error' => 'Product not found'], 404);
//             }
//             return response()->json(['data' => $product], 200);
//         }
//     } catch (ValidationException $e) {
//         $errorMessages = implode(', ', $e->validator->errors()->all());
//         return response()->json(['error' => $errorMessages], 400);
//     }
// }

public function getProduct(Request $request, $productId = null)
{
    try {
        if (!$productId) {
            // Fetch all products that are not disabled
            $products = Product::with('discounts', 'bakery', 'reviews.user', 'category')
                               ->where('disabled', false)
                               ->get();
            
            if ($products->isEmpty()) {
                return response()->json(['error' => 'No products found'], 404);
            }

            return response()->json(['data' => $products], 200);
        } else {
            // Fetch the product by ID
            $product = Product::with('discounts', 'bakery', 'reviews.user', 'category')
                              ->where('disabled', false) // Filter out disabled products
                              ->find($productId);

            if (!$product) {
                return response()->json(['error' => 'Product not found'], 404);
            }

            return response()->json(['data' => $product], 200);
        }
    } catch (ValidationException $e) {
        $errorMessages = implode(', ', $e->validator->errors()->all());
        return response()->json(['error' => $errorMessages], 400);
    }
}


public function getProductsByBakery($bakeryId)
{
    $products = Product::with('discounts', 'bakery','reviews.user','category')->where('bakery_id', $bakeryId)->get();
    return response()->json(['data' => $products], 200);
}
public function getProductsByCategory($categoryName)
{
    $products = Product::with('discounts', 'bakery', 'reviews.user', 'category')
        ->whereHas('category', function ($query) use ($categoryName) {
            $query->where('name', $categoryName);
        })
        ->get();

    return response()->json(['data' => $products], 200);
}




public function getProductsWithDiscount()
{
    try {
        // Get products with discounts
        $products = Product::has('discounts')->with('discounts', 'bakery','reviews.user','category')->get();
        return response()->json(['data' => $products], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'An error occurred while fetching products with discounts'], 500);
    }
}

public function disableProduct($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->disabled = true;
            $product->save();
            
            return response()->json(['message' => 'Product disabled successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
 
    public function enableProduct($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->disabled = false;
            $product->save();
            
            return response()->json(['message' => 'Product enabled successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function addReview(Request $request, $productId)
{
    try {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'description' => 'required|string',
            'rating' => 'required|min:1|max:5',
        ]);

        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $review = $product->reviews()->create($validatedData);

      
        $product->rating = $product->reviews()->avg('rating');
        $product->reviews_count = $product->reviews()->count();
        $product->save();
        Feedback::where('user_id',$validatedData['user_id'])->where('product_id',$validatedData['product_id'])->delete();
        return response()->json(['success' => 'Review product added successfully', 'data' => $review], 200);
    } catch (ValidationException $e) {
        $errorMessages = implode(', ', $e->validator->errors()->all());
        return response()->json(['error' => $errorMessages], 400);
    }
}

}
