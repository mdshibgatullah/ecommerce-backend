<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(){
        $orders = Order::orderBy('created_at', 'DESC')->get();
        return response()->json([
            'status' => 200,
            'data' => $orders
        ]);
    }


    public function detalis($id){
        $order = Order::with('items', 'items.product')->find($id);

        if($order == null){
            return response()->json([
                'data' => [],
                'message' => 'Order not Found',
                'status' => 404
            ], 404);
        }
        return response()->json([
            'status' => 200,
            'data' => $order
        ]);
    }



    public function updateOrder(Request $request, $id){

     $order = Order::findOrFail($id);

        if(!$order){
            return response()->json([
                'status' => 404,
                'message' => 'Order not found'
            ], 404);
        }

        $order->status = $request->status;
        $order->payment_status = $request->payment_status;
        $order->save();
        

        return response()->json([
            'status' => 200,
            'message' => 'Order updated successfully',
            'data' => $order
        ]);
    }
}
