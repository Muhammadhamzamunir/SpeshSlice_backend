<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgetPassword as ForgetPasswordMail;

class ForgetPassword extends Controller
{
    public function forgetPassword(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'email' => 'required|email',
            ]);

            $user = User::where('email', $validatedData['email'])->first();
            if (!$user) {
                return response()->json(["error" => "User not found"], 404);
            }

            // Generate a random password
            $newPassword = Str::random(12);

            // Update user's password
            $user->password = bcrypt($newPassword);
            $user->save();

            // Send email with new password
            Mail::to($user->email)->send(new ForgetPasswordMail($user->name, $newPassword));

            return response()->json(["message" => "Password reset email sent."]);

        } catch (ValidationException $e) {
            $errorMessages = implode(', ', $e->validator->errors()->all());
            return response()->json(['error' => $errorMessages], 400);
        }
    }
}

