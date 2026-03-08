<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TempImageController extends Controller
{

   public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        if ($request->hasFile('image')) {

            $file = $request->file('image');

            $fileName = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();

            $file->storeAs('product', $fileName, 'public');

            $tempImage = TempImage::create([
                'image' => $fileName
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Image uploaded successfully',
            'data' => [
                'id' => $tempImage->id,
                'image' => $tempImage->image
            ]
        ], 200);
    }
}