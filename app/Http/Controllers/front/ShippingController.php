<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Shipping;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function getShipping(){
        $shipping = Shipping::first();
        return response()->json([
            'status' => 200,
            'data' => $shipping
        ]);
    }
}
