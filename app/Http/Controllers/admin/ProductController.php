<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSize;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{

    public function index(){
        $products = Product::orderBy('created_at', 'DESC')
            ->with(['product_images','product_sizes'])
            ->get();

        return response()->json([
            'status' => 200,
            'data' => $products
        ]);
    }

    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'price' => 'required',
            'category' => 'required|integer',
            'brand' => 'required|integer',
            'sku' => 'required|unique:products,sku',
            'is_featured' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ],422);
        }

        $product = Product::create([
            'title'=>$request->title,
            'price'=>$request->price,
            'compare_price'=>$request->compare_price,
            'category_id'=>$request->category,
            'brand_id'=>$request->brand,
            'sku'=>$request->sku,
            'qty'=>$request->qty,
            'description'=>$request->description,
            'short_description'=>$request->short_description,
            'status'=>$request->status,
            'barcode'=>$request->barcode,
            'is_featured'=>$request->is_featured,
        ]);


        if(!empty($request->sizes)){
            ProductSize::where('product_id',$product->id)->delete();

            foreach ($request->sizes as $sizeId) {
                ProductSize::create([
                    'product_id'=>$product->id,
                    'size_id'=>$sizeId,
                ]);
            }
        }


        if (!empty($request->gallery)) {

            foreach ($request->gallery as $key => $tempImgId) {

                $tempImage = TempImage::find($tempImgId);
                if(!$tempImage) continue;

                $sourcePath = storage_path('app/public/product/'.$tempImage->image);

                if(!file_exists($sourcePath)) continue;

                $ext = pathinfo($tempImage->image, PATHINFO_EXTENSION);
                $newFileName = time().'_'.uniqid().'.'.$ext;

                copy($sourcePath, storage_path('app/public/product/'.$newFileName));

                ProductImage::create([
                    'image'=>$newFileName,
                    'product_id'=>$product->id
                ]);

                if($key == 0){
                    $product->image = $newFileName;
                    $product->save();
                }
            }
        }

        return response()->json([
            'status'=>200,
            'message'=>'Product created successfully',
            'data'=>$product
        ]);
    }


    public function show($id){

        $product = Product::with(['product_images','product_sizes'])->find($id);

        if(!$product){
            return response()->json([
                'status'=>404,
                'message'=>'Product not found',
                'data'=>[]
            ],404);
        }

        $productSizes = $product->product_sizes()->pluck('size_id');

        return response()->json([
            'status'=>200,
            'data'=>$product,
            'productSizes'=>$productSizes
        ]);
    }


    public function update(Request $request,$id){

        $product = Product::with('product_images')->findOrFail($id);

        $validator = Validator::make($request->all(),[
            'title'=>'required|string',
            'price'=>'required',
            'category'=>'required|integer',
            'sku'=>'required|unique:products,sku,'.$id,
            'is_featured'=>'required',
            'status'=>'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'status'=>422,
                'errors'=>$validator->errors(),
            ],422);
        }

        $product->update([
            'title'=>$request->title,
            'price'=>$request->price,
            'compare_price'=>$request->compare_price,
            'category_id'=>$request->category,
            'brand_id'=>$request->brand,
            'sku'=>$request->sku,
            'qty'=>$request->qty,
            'description'=>$request->description,
            'short_description'=>$request->short_description,
            'status'=>$request->status,
            'barcode'=>$request->barcode,
            'is_featured'=>$request->is_featured,
        ]);


        if(!empty($request->sizes)){
            ProductSize::where('product_id',$product->id)->delete();

            foreach ($request->sizes as $sizeId) {
                ProductSize::create([
                    'product_id'=>$product->id,
                    'size_id'=>$sizeId,
                ]);
            }
        }


        if (!empty($request->old_images)) {

            foreach ($product->product_images as $img) {

                if (!in_array($img->id,$request->old_images)) {

                    @unlink(storage_path('app/public/product/'.$img->image));

                    $img->delete();
                }
            }
        }


        if (!empty($request->gallery)) {

            foreach ($request->gallery as $tempImgId) {

                $tempImage = TempImage::find($tempImgId);
                if(!$tempImage) continue;

                $sourcePath = storage_path('app/public/product/'.$tempImage->image);

                if(!file_exists($sourcePath)) continue;

                $ext = pathinfo($tempImage->image, PATHINFO_EXTENSION);
                $newFileName = time().'_'.uniqid().'.'.$ext;

                copy($sourcePath, storage_path('app/public/product/'.$newFileName));

                ProductImage::create([
                    'product_id'=>$product->id,
                    'image'=>$newFileName
                ]);
            }
        }


        $firstImage = ProductImage::where('product_id',$product->id)->first();

        $product->image = $firstImage ? $firstImage->image : null;
        $product->save();


        return response()->json([
            'status'=>200,
            'message'=>'Product updated successfully',
            'data'=>Product::with('product_images')->find($id)
        ]);
    }


    public function destroy($id){

        $product = Product::with('product_images')->find($id);

        if(!$product){
            return response()->json([
                'status'=>404,
                'message'=>'Product not found'
            ],404);
        }

        if ($product->image) {
            @unlink(storage_path('app/public/product/'.$product->image));
        }

        foreach ($product->product_images as $img) {

            @unlink(storage_path('app/public/product/'.$img->image));

            $img->delete();
        }

        ProductSize::where('product_id',$product->id)->delete();

        $product->delete();

        return response()->json([
            'status'=>200,
            'message'=>'Product deleted successfully',
        ]);
    }
}