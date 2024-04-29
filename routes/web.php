<?php

use Illuminate\Support\Facades\Route;
use Tymon\JWTAuth\Facades\JWTAuth;
Route::get('/', function () {
//   $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNzEwMjQ2OTY2LCJleHAiOjE3MTAyNTA1NjYsIm5iZiI6MTcxMDI0Njk2NiwianRpIjoieEVVMHh0SnhpUEVtSVoyYiIsInN1YiI6ImEwODQ3ZGVmLTJmNGItNDdkZC05ZTEyLTUwZDgyNTJlMjQ0OSIsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.ByMyrFuzW1e_xCxxV9Z3wBT7ux5GeSyyoZmkrKgLLqM";
//   try {
//     // Set the token and get the payload
//     $payload = JWTAuth::setToken($token)->getPayload();

//     // Check if the token has an expiration time
//     if ($payload->hasKey('exp')) {
//         // Get the expiration timestamp from the payload
//         $expirationTimestamp = $payload['exp'];

//         // Get the current timestamp
//         $currentTimestamp = time();

//         // Calculate the remaining time in seconds
//         $remainingTime = $expirationTimestamp - $currentTimestamp;

//         if ($remainingTime > 0) {
//             // Convert remaining time to minutes, hours, and days
//             $remainingMinutes = floor($remainingTime / 60);
//             $remainingHours = floor($remainingMinutes / 60);
//             $remainingDays = floor($remainingHours / 24);

//             // Output the remaining time
//             echo 'Token is still valid. Remaining time: ';
//             echo $remainingDays > 0 ? $remainingDays . ' days, ' : '';
//             echo $remainingHours % 24 . ' hours, ';
//             echo $remainingMinutes % 60 . ' minutes.';
//         } else {
//             // Token has expired
//             echo 'Token has expired. User needs to reauthenticate.';
//         }
//     } else {
//         // Token does not have an expiration time
//         echo 'Token does not have an expiration time.';
//     }
// } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
//     // Handle JWT decoding exceptions
//     echo 'Error decoding or checking the token: ' . $e->getMessage();
// }
  $data = ['WEB.PHP' => 'WHY THERE'];
  return response()->json($data, 200);
});

