<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function saveOrder(Request $request)
    {
        if (empty($request->cart)) {
            return response()->json([
                'status' => 400,
                'message' => 'Your cart is empty'
            ], 400);
        }

        $user = Auth::user();

        $order = Order::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'email' => $request->email,
            'address' => $request->address,
            'mobile' => $request->mobile, 
            'state' => $request->state,
            'zip' => $request->zip,
            'city' => $request->city,
            'grand_total' => $request->grand_total,
            'subtotal' => $request->subtotal,
            'discount' => $request->discount,
            'shipping' => $request->shipping,
            'payment_status' => $request->payment_status,
            'status' => $request->status,
        ]);

        foreach ($request->cart as $item) {

            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $item['product_id'],
                'unit_price' => $item['price'],
                'qty'        => $item['qty'],
                'price'      => $item['qty'] * $item['price'],
                'size'       => $item['size'] ?? null,
                'name'       => $item['title'],
            ]);
        }

        return response()->json([
            'status' => 200,
            'id' => $order->id,
            'message' => 'You have successfully placed your order'
        ], 200);
    }
}