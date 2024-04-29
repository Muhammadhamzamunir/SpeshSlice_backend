<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;
use Closure;
use JWTAuth;
use Exception;
class loginController extends Controller
{
 


    public function updateUser(Request $request, $id) {
       
        $validatedData = $request->validate([
            'username' => 'required',
            'phone' => 'required',
            'profile_url' => 'required',
        ]);
        $user = User::find($id);
        if (!$user) {
            return response()->json(["error" => "User not found"], 404);
        }
        
        $user->update([
            'username' => $validatedData['username'],
            'phone' => $validatedData['phone'],
            'profile_url' => $validatedData['profile_url'],
        ]);
        return response()->json(["success" => "User updated", 'data' => $user], 200);
    }
    



    public function changePassword(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'oldPassword' => 'required|string',
                'newPassword' => 'required|string|min:8',
            ]);
    
            $user = Auth::user(); 
    
           
            if (!Hash::check($validatedData['oldPassword'], $user->password)) {
                return response()->json(['error' => 'Old password is incorrect'], 400);
            }   
            $newPassword = bcrypt($validatedData['newPassword']);
            $user->password = $newPassword;
            $user->save();
    
            return response()->json(['success' => 'Password changed successfully'], 200);
        } catch (ValidationException $e) {
            $errorMessages = implode(', ', $e->validator->errors()->all());
            return response()->json(['error' => $errorMessages], 400);
        }
    }
    


    public function login(Request $request)
    {
        
        $credentials = request(['email', 'password']);
        $rememberMe = !empty($request->input("remember_me"));
        $expirationTime = $rememberMe ? 43800 : 60; 
        if (!$token =  $rememberMe? auth()->attempt($credentials, $rememberMe):auth()->attempt($credentials)) {
            return response()->json(['error' => 'Wrong Creditionals'], 401);
        }
    
        $user = User::with('bakery','address')->where('email', $request->input('email'))->first();
    
        $customExpiration = Carbon::now()->addMinutes($expirationTime)->timestamp;
    
        $token = JWTAuth::claims(['exp' => $customExpiration])->fromUser($user);
        
       
        return $this->respondWithToken($token, $user, $rememberMe);
    }
    
    
    protected function respondWithToken($token,$user,$rememberMe)
        {
            return response()->json([
                "success" => "Welcome,$user->username !",
                'token'=>$token,
                'rememberMe' => $rememberMe,
                'user'=>$user
            ]);
        }






    // function loginHandle(Request $request){
    //    try{
    //     $validatedData = $request->validate([
    //         "email"=>"required|email",
    //         "password"=>"required"
    //        ]);
        
    //      $userRecord = DB::table('users')->where("email", $validatedData["email"])->where("password",$validatedData["password"])->first();
    //       if($userRecord){
    //         return response()->json(["success" => "Welcome, $userRecord->username!"], 200);

    //       }else{
    //         return response()->json(["error"=>"Something Went Wrong"], 400);
    //       }
        
    //    }catch(ValidationException $e){
    //      return response()->json(["error"=>"Something Went Wrong"], 400);
    //    }
    // }


    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    // try {
        //     // Attempt to verify the token
        //     $user = JWTAuth::parseToken()->authenticate();
        //     return response()->json(['error' => 'token'], 200);
        // } catch (Exception $e) {
        //     // Token is invalid or not provided
        //     return response()->json(['error' =>$request], 401);
        // }
  








    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['success' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */



    
}
