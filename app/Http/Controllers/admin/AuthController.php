<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request){
        $validator = [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ];

        $validator = Validator::make($request->all(), $validator);

            if($validator->fails()) {
                return response()->json([
                'status' => 400,
                'errors'  => $validator->errors()
            ], 400);
        }


        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => 'admin',
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

        // Role check
        if ($user->role !== 'admin') {
            return response()->json([
                'status'  => false,
                'message' => 'You are not authorized to access admin panel',
            ], 403);
        }

        // Create token
        $token = $user->createToken('admin-token')->plainTextToken;

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

    public function user()
    {
        $user = User::where('role', 'customer')->get();

        return response()->json([
            'status' => 200,
            'data' => $user
        ]);
    }
}
