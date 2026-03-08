<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    public function register(Request $request){
        $roles = [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ];

        $validator = Validator::make($request->all(), $roles);

            if($validator->fails()) {
                return response()->json([
                'status' => 400,
                'errors'  => $validator->errors()
            ], 400);
        }


        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => 'customer',
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
                'status' => 200,
                'message'  => 'Registration Success'
            ], 200);
        
    }


    public function login(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Attempt login
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid email or password',
            ], 401);
        }

        // Authenticated user
        $user = Auth::user();

        // Create token
        $token = $user->createToken('token')->plainTextToken;

        return response()->json([
            'status' => 200,
            'token'  => $token,
            'id'   => $user->id,
            'name'   => $user->name,
        ], 200);
    }

    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email',$request->email)->first();

        if(!$user){
            return response()->json([
                'status' => 400,
                'message' => 'Email not found'
            ]);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Email found'
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed'
        ]);

        $user = User::where('email',$request->email)->first();

        if(!$user){
            return response()->json([
                'status'=>400,
                'message'=>'User not found'
            ]);
        }

        $user->update([
            'password'=>Hash::make($request->password)
        ]);

        return response()->json([
            'status'=>200,
            'message'=>'Password updated successfully'
        ]);
    }


    public function orderDetails($id, Request $request)
    {
        $order = Order::with('items')->where([
            'user_id' => $request->user()->id,
            'id' => $id
        ])
        ->with('items', 'items.product')
        ->first();

        if ($order == null) {
            return response()->json([
                'status' => 404,
                'message' => 'Order not found.',
                'data' => []
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => $order
        ], 200);
    }


    public function getOrders(Request $request)
    {
        $order = Order::where('user_id' ,$request->user()->id)->get();

        return response()->json([
            'status' => 200,
            'data' => $order
        ], 200);
    }

    public function updateAccount(Request $request)
    {
        $user = User::findOrFail($request->user()->id);

        if($user == null){
            return response()->json([
            'status' => 400,
            'message'  => 'User not found'
        ], 400);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$request->user()->id.',id',
            'city' => 'required|max:100',
            'state' => 'required|max:100',
            'zip' => 'required|max:100',
            'mobile' => 'required|max:100',
            'address' => 'required|max:200',
        ]);

        if($validator->fails()) {
            return response()->json([
            'status' => 400,
            'errors'  => $validator->errors()
        ], 400);
        }

       $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'city' => $request->city,
            'state' => $request->state,
            'zip' => $request->zip,
            'mobile' => $request->mobile,
            'address' => $request->address,
        ]);

        return response()->json([
            'status' => 200,
            'message'  => 'Profile update successfully',
            'data' => $user
        ], 200);
    }


    public function accountDetails(Request $request)
    {
        $user = User::find($request->user()->id);

        if($user == null){
            return response()->json([
            'status' => 400,
            'message'  => 'User not found'
        ], 400);
        }


        return response()->json([
            'status' => 200,
            'data' => $user
        ], 200);
    }
}
