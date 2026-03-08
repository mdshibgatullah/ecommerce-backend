<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Shipping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShippingController extends Controller
{
    public function getShipping(){
        $shipping = Shipping::first();
        return response()->json([
            'status' => 200,
            'data' => $shipping
        ]);
    }

    public function saveOrUpdate(Request $request) {
        $validator = Validator::make($request->all(), [
            'shipping_charge' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ], 422);
            }

        Shipping::updateOrInsert([
            'id' => 1
        ], [
            'shipping_charge' => $request->shipping_charge
        ]);

    //    $shipping = Shipping::find(1);

    //     if($shipping == null){
    //         Shipping::create([
    //             'shipping_charge' => $request->shipping_charge
    //         ]);
    //     }else{
    //         $shipping->update([
    //             'shipping_charge' => $request->shipping_charge
    //         ]);
    //     }

        return response()->json([
            'status' => 200,
            'message' => 'Shipping saved successfully',
        ], 200);
    }
}
