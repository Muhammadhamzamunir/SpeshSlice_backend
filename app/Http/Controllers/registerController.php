<?php


namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use App\Models\User;
class registerController extends Controller
{
    function registrationHandle(Request $request)
    {
        
        try {
            $validatedData = $request->validate([
                'username' => 'required|string|max:255',
                'email' => 'required|email|unique:users|max:255',
                'password' => 'required|string|min:8',
                'phone'=>'required'
            ]);
          
            $password= bcrypt($validatedData["password"]);
            User::create([...$validatedData,  "password"=>$password]);

            return response()->json(['success' => 'User registered successfully'], 200);

        } catch (ValidationException $e) {
            $errorMessages = implode(', ', $e->validator->errors()->all());
            return response()->json(['error' => $errorMessages], 400);

        }
    }
}
