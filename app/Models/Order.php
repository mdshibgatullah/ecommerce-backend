<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'city',
        'state',
        'zip',
        'mobile',
        'address',
        'grand_total',
        'subtotal',
        'shipping',
        'discount',
        'payment_method',
        'payment_status',
        'status',
        'cart',

    ];



    public function items(){
        return $this->hasMany(OrderItem::class);
    }
    


    protected function casts(): array
    {
        return [
            'created_at'=> 'datetime:d M, Y'
        ];
    }
}
