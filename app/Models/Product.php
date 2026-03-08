<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'title',
        'price',
        'discount_price',
        'category_id',
        'brand_id',
        'sku',
        'qty',
        'description',
        'short_description',
        'status',
        'barcode',
        'is_featured',
        'image',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute(){

        if($this->image == ""){
            return "";
        }

        return asset('storage/product/'.$this->image);
    }

    public function product_images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function product_sizes()
    {
        return $this->hasMany(ProductSize::class);
    }
}