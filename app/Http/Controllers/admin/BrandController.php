<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
        // return brand
    public function index(){
        $brands = Brand::orderBy('created_at', 'DESC')->get();
        return response()->json([
            'status' => 200,
            'data' => $brands
        ]);
    }


    // store brand
    public function store(Request $request){
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $brand = Brand::create([
                'name' => $request->name,
                'status' => $request->status,
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Brand created successfully',
                'data' => $brand
            ]);

    }


    // show brand
    public function show($id){
        $brand = Brand::find($id);
       
        if($brand == null){
            return response()->json([
                'status' => 404,
                'message' => 'Brand not found',
                'data' => []
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => $brand
        ]);

    }


    // update brand
    public function update(Request $request, $id)
    {
        $brand = Brand::findOrFail($id);

        if (!$brand) {
            return response()->json([
                'status' => 404,
                'message' => 'Brand not found',
                'data' => []
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'status' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $brand->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Brand updated successfully',
            'data' => $brand
        ], 200);
    }


    // destroy brand
    public function destroy($id){
        $brand = Brand::findOrFail($id);
       
        if($brand == null){
            return response()->json([
                'status' => 404,
                'message' => 'Brand not found',
                'data' => []
            ], 404);
        }

        $brand->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Brand deleted successfully',
            'data' => $brand
        ], 200);
    }
}
