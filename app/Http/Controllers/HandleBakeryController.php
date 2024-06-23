<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Bakery;
use App\Models\Address;
use App\Models\User;
use App\Models\Feedback;
use App\Models\UserAddresses;
use App\Models\Product;
class HandleBakeryController extends Controller
{

//     public function getBakeries($id = null)
// {
//     try {
//         if ($id) {
//             $bakery = Bakery::with('address', 'products.category', 'products.discounts', 'reviews.user')->find($id);
        
//             if (!$bakery) {
//                 return response()->json(['error' => 'Bakery not found'], 404);
//             }

//             return response()->json(['data' => $bakery]);
//         } else {
//             $bakeries = Bakery::with('address','products.category', 'products.discounts','reviews.user')->get();
//             return response()->json(['data' => $bakeries]);
//         }
//     } catch (\Exception $e) {
//         return response()->json(['error' => $e->getMessage()], 500);
//     }
// }

public function getBakeries($id = null)
{
    try {
        if ($id) {
            $bakery = Bakery::with('address', 'products.category', 'products.discounts', 'reviews.user')
                            ->where('disabled', false) 
                            ->find($id);
        
            if (!$bakery) {
                return response()->json(['error' => 'Bakery not found'], 404);
            }

            return response()->json(['data' => $bakery]);
        } else {
            $bakeries = Bakery::with('address', 'products.category', 'products.discounts', 'reviews.user')
                              ->where('disabled', false) // Filter out disabled bakeries
                              ->get();
            return response()->json(['data' => $bakeries]);
        }
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
 


    function registerBakeryhandle(Request $request) {
        try {
            // Validate bakery and address attributes
            $validatedData = $request->validate([
                'owner_name' => 'required|string|max:255',
                'user_id' => 'required',
                'email' => 'required|email|unique:bakeries|max:255',
                'business_name' => 'required|string',
                'specialty' => 'required|string|max:255',
                'timing' => 'required|string|max:255',
                'phone' => 'required|string|max:255',
                'country' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'street' => 'required|string|max:255',
                'longitude' => 'required|string|max:255',
                'latitude' => 'required|string|max:255',
                'price_per_pound' => 'required|numeric',
                'price_per_decoration' => 'required|numeric',
                'price_per_tier' => 'required|numeric',
                'price_for_shape' => 'required|numeric',
                'tax' => 'required|numeric', 'logo_url'=>'required',
                'description'=>'required'
            ]);
    
            // Create bakery
            $bakery = Bakery::create([
                'owner_name' => $validatedData['owner_name'],
                'user_id' =>$request->input('user_id'),
                'email' => $validatedData['email'],
                'business_name' => $validatedData['business_name'],
                'specialty' => $validatedData['specialty'],
                'timing' => $validatedData['timing'],
                'phone' => $validatedData['phone'],
                'price_per_pound' => $validatedData['price_per_pound'],
                'price_per_decoration' => $validatedData['price_per_decoration'],
                'price_per_tier' => $validatedData['price_per_tier'],
                'price_for_shape' => $validatedData['price_for_shape'],
                'tax' => $validatedData['tax'],  
                'logo_url'=>$validatedData['logo_url'],  
                'description'=>$validatedData['description']   
            ]);
          $default=true;
            // Create address
            $address = Address::create([
                'entity_id' => $bakery->id,
                'country' => $validatedData['country'],
                'city' => $validatedData['city'],
                'street' => $validatedData['street'],
                'longitude' => $validatedData['longitude'],
                'latitude' => $validatedData['latitude'],
                'default'=>$default
            ]);
    
          $user = User::findOrFail($request->input('user_id'));
            if($user){
                $user->update(['isBakeryRegistered'=>true]);
            }

            return response()->json(['success' => 'Bakery registered successfully',"data"=>$bakery], 200);
    
        } catch (ValidationException $e) {
            $errorMessages = implode(', ', $e->validator->errors()->all());
            return response()->json(['error' => $errorMessages], 400);
        }
    }

    public function updateBakery(Request $request, $id) {
        // return response()->json($request->all(), 200, );
       try{ 
        $validatedData = $request->validate([
            'owner_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'business_name' => 'required|string|max:255',
            'specialty' => 'required|string|max:255',
            'timing' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'price_per_pound' => 'required|numeric',
            'price_per_decoration' => 'required|numeric',
            'price_per_tier' => 'required|numeric',
            'price_for_shape' => 'required|numeric',
            'tax' => 'required|numeric',
            'logo_url'=>'required',
            'description'=>'required'
        ]);
    
        // Find the bakery record
        $bakery = Bakery::findOrFail($id);
    
        // Update bakery record
        $bakery->update($validatedData);
    
        return response()->json(["success"=>"Record Has Been Updated"], 200);
    } catch (ValidationException $e) {
        $errorMessages = implode(', ', $e->validator->errors()->all());
        return response()->json(['error' => $errorMessages], 400);
    }
    }
    

    public function deleteBakery(Request $request, $id){

        $bakery = Bakery::findOrFail($id);
        if($bakery){
            $bakery->delete();
            return response()->json(["success"=>"OOPs! Store Removed"], 200);
        }else{
            return response()->json(["error"=>"What are you doing Bro"], 200);

        }
    }


    public function addBakeryReview(Request $request, $bakeryId)
{
    try {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'bakery_id' => 'required|exists:products,id',
            'description' => 'required|string',
            'rating' => 'required|min:1|max:5',
        ]);

        $bakery = Bakery::find($bakeryId);
        if (!$bakery) {
            return response()->json(['error' => 'not found'], 404);
        }

        $review = $bakery->reviews()->create($validatedData);

      
        $bakery->averageRating = $bakery->reviews()->avg('rating');
        $bakery->rating_count = $bakery->reviews()->count();
        $bakery->save();
     Feedback::where('user_id',$validatedData['user_id'])->where('bakery_id',$validatedData['bakery_id'])->where('Is_bakery',1)->delete();
        return response()->json(['success' => 'Review added successfully', 'data' => $review], 200);
    } catch (ValidationException $e) {
        $errorMessages = implode(', ', $e->validator->errors()->all());
        return response()->json(['error' => $errorMessages], 400);
    }
}

public function getBakeriesNearUser($userId)
{
    // Fetch user's default address coordinates
    $userDefaultAddress = UserAddresses::where('user_id', $userId)
        ->where('default', true)
        ->first();

    // Ensure user has a default address
    if (!$userDefaultAddress) {
        return response()->json(["data" => [], "message" => "No default address found for the user"], 404);
    }

    $userLat = $userDefaultAddress->latitude;
    $userLon = $userDefaultAddress->longitude;

    // Perform a raw SQL query using DB::raw to calculate distances
    $bakeries = Bakery::select('bakeries.*', 'distances.distance AS distance')
        ->join(
            DB::raw(
                '(SELECT bakeries.id, (6371 * acos(cos(radians(CAST(? AS numeric))) * cos(radians(CAST(latitude AS numeric))) * cos(radians(CAST(longitude AS numeric)) - radians(CAST(? AS numeric))) + sin(radians(CAST(? AS numeric))) * sin(radians(CAST(latitude AS numeric))))) AS distance
                FROM bakeries
                LEFT JOIN addresses ON addresses.entity_id = bakeries.user_id
                WHERE addresses.default = ?::boolean
                ORDER BY distance ASC) AS distances'
            ),
            function ($join) {
                $join->on('bakeries.id', '=', 'distances.id');
            }
        )
        ->where('distance', '<=', 10)
        ->setBindings([$userLat, $userLon, $userLat, true, 10])
        ->get();

    $bakeriesWithProducts = [];

    foreach ($bakeries as $bakery) {
        $products = Product::with('discounts')->where('bakery_id', $bakery->id)->get();
        $bakeriesWithProducts[] = [
            'bakery' => $bakery,
            'products' => $products
        ];
    }

    return response()->json(["data" => $bakeriesWithProducts], 200);
}

public function disableBakery($id)
{
    $bakery = Bakery::findOrFail($id);
    $bakery->disabled = true;
    $bakery->save();

    // Disable related products
    $products = $bakery->products;
    foreach ($products as $product) {
        $product->disabled = true;
        $product->save();
    }

    return response()->json(['message' => 'Bakery and its products disabled successfully']);
}

public function enableBakery($id)
{
    $bakery = Bakery::findOrFail($id);
    $bakery->disabled = false;
    $bakery->save();

    // Enable related products that were previously disabled due to bakery disablement
    $products = $bakery->products()->where('disabled', true)->get();
    foreach ($products as $product) {
        $product->disabled = false;
        $product->save();
    }

    return response()->json(['message' => 'Bakery and its products enabled successfully']);
}

public function updateBakeryDetails(Request $request, $id)
{
    // Validate the request data
    $validatedData = $request->validate([
        'owner_name' => 'required|string|max:255',
        'business_name' => 'required|string|max:255',
        'specialty' => 'required|string|max:255',
        'timing' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:20',
        'description' => 'nullable|string',
        'price_per_tier'=>'nullable|numeric',
        'price_per_pound'=>'nullable|numeric',
        'tax'=>'nullable|numeric',
        'price_per_decoration'=>'nullable|numeric',
    ]);

    // Find the bakery record by ID
    $bakery = Bakery::findOrFail($id);

    // Update the bakery details
    $bakery->update($validatedData);

    // Return a success response
    return response()->json([
        'message' => 'Bakery details updated successfully',
        'data' => $bakery
    ], 200);
}
}
