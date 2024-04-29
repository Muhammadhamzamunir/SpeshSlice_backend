<?php

namespace App\Http\Controllers;
use App\Models\Address;
use App\Models\UserAddresses;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    function getAddresses($id = null){
        $entityId = $id;
        if ($entityId) {
            $addresses = Address::where('entity_id', $entityId)->get();

        if ($addresses->isEmpty()) {
            
            return response()->json(['error' => 'No addresses found for the specified entity ID'], 404);
        }

        return response()->json(["data"=>$addresses], 200);
        } 
    }

    public function saveAddresses(Request $request, $id = null) {
        $entityId = $id;
    
        $validatedData = $request->validate([
            'country' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'longitude' => 'required|string|max:255',
            'latitude' => 'required|string|max:255',
                
        ]);
    
        if ($validatedData['default'] === "true") {
            // return response()->json($validatedData['default'], 200, );
            // Check if any existing address for the entity has default true
            $existingDefaultAddress = Address::where('entity_id', $entityId)
                ->where('default', true)
                ->first();
    
            if ($existingDefaultAddress) {
                // If found, set default to false
                $existingDefaultAddress->update(['default' => false]);
            }
        }
    
        // Create new address
        $address = Address::create([
            'entity_id' => $entityId,
            'country' => $validatedData['country'],
            'city' => $validatedData['city'],
            'street' => $validatedData['street'],
            'longitude' => $validatedData['longitude'],
            'latitude' => $validatedData['latitude'],
            'default' => $validatedData['default']
        ]);
    
        return response()->json(["success"=>"Address Added" ,'data'=>$address], 200);
    }
    public function saveUserAddresses(Request $request, $id = null) {
        $user_id = $id;
    
        $validatedData = $request->validate([
            'country' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'longitude' => 'required|string|max:255',
            'latitude' => 'required|string|max:255',
            'default'=>"sometimes"
        ]);
    

      
        if ($validatedData['default'] == "true") {
          
      
            $existingDefaultAddress = UserAddresses::where('user_id', $user_id)
                ->where('default', true)
                ->first();
    
            if ($existingDefaultAddress) {
            
                $existingDefaultAddress->update(['default' => false]);
            }
        }
    
        // Create new address
         $address = UserAddresses::create([
            'user_id' => $user_id,
            'country' => $validatedData['country'],
            'city' => $validatedData['city'],
            'street' => $validatedData['street'],
            'longitude' => $validatedData['longitude'],
            'latitude' => $validatedData['latitude'],
            'default' => $validatedData['default']
        ]);

        $allAddresses = UserAddresses::where('user_id', $user_id)->get();

    
        return response()->json(["success"=>"Address Added" ,'data'=>$allAddresses], 200);
    }
    public function setDefaultAddress($addressId) {
        
        $address = UserAddresses::find($addressId);
        
        if (!$address) {
            return response()->json(["error" => "Address not found"], 404);
        }
        
        $user_id = $address->user_id;
        
        // Set the selected address as default
        $address->update(['default' => true]);
        
        // Set other addresses of the same user as non-default
        UserAddresses::where('user_id', $user_id)
            ->where('id', '<>', $addressId) // Exclude the current address
            ->update(['default' => false]);
        
        // Retrieve and return all addresses after the update
        $allAddresses = UserAddresses::where('user_id', $user_id)->get();
        
        return response()->json(["success" => "Default Address Updated", 'data' => $allAddresses], 200);
    }
    
    public function deleteUserAddresses(Request $request, $id){
        $address = UserAddresses::find($id);
        
        if (!$address) {
            return response()->json(["error" => "Address not found"], 404);
        }
        
        $user_id = $address->user_id; // Retrieve user_id from the address object
        
        // Check if the address being deleted is the default one
        if ($address->default) {
            // Find another address belonging to the same user
            $newDefaultAddress = UserAddresses::where('user_id', $user_id)
                ->where('id', '<>', $id) // Exclude the current address
                ->first();
            
            // If another address is found, set it as the new default
            if ($newDefaultAddress) {
                $newDefaultAddress->update(['default' => true]);
            }
        }
        
        // Delete the address
        $address->delete();
        
        // Retrieve and return all addresses after the update
        $allAddresses = UserAddresses::where('user_id', $user_id)->get();
        
        return response()->json(["success" => "Address Deleted", 'data' => $allAddresses], 200);
    }
    
    
    
}
