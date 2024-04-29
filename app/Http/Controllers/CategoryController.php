<?php

namespace App\Http\Controllers;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
class CategoryController extends Controller
{
    public function getCategories(Request $request)
{
    
    $categories = Category::withCount('products')->get();

    
    return response()->json(['data' => $categories], 200);
}


    public function registerCategory(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|unique:categories',
                'image_url' => 'required'
            ]); 
    
            Category::create($validatedData);
            return response()->json(["success" => "Category Stored Successfully"], 200);
        } catch (ValidationException $e) {
            $errorMessages = implode(', ', $e->validator->errors()->all());
            return response()->json(['error' => $errorMessages], 400);
        }
    }
    
}
