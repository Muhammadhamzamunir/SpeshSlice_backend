<?php

namespace App\Http\Controllers;

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
}
