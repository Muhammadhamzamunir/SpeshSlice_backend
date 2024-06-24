<?php

namespace App\Http\Controllers;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentInfo;
class PaymentInfoHandle extends Controller
{
    function getPaymentinfo(Request $request, $id = null){
     if($id){
        $info  = PaymentInfo::with('bakery')->where('bakery_id',$id)->first();
        return response()->json(["data"=>$info], 200 );
     }else{
        $info  = PaymentInfo::with('bakery')->get();
        return response()->json(["data"=>$info], 200);
     }
    }

    function deductAmount(Request $request,$id){
       try{
         $validatedData = $request->validate([
            'amount'=>'required|numeric|min:0'
          ]);
        
          $info  = PaymentInfo::with('bakery')->where('bakery_id',$id)->first();
          if($info->payment<$validatedData['amount']){
            return response()->json(['error'=>'Amount is greater'], 200);
          }else{
            $info->payment -= $validatedData['amount'];
            $info->save();
           return response()->json(['success'=>'Success'], 200);
          }
         

       }catch (ValidationException $e) {
         $errorMessages = implode(', ', $e->validator->errors()->all());
         return response()->json(['error' => $errorMessages], 400);
     } catch (\Exception $e) {
         Log::error('An error occurred ' . $e->getMessage());
         return response()->json(['error' => 'An error occurred ',"errrr"=>$e->getMessage()], 500);
     }
    }
}
