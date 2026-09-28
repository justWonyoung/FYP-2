<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class CustomerOrderItem extends Model
{

    use HasFactory;


    protected $fillable = [

        'customer_order_id',
        'product_name',
        'quantity',
        'price',
        'subtotal'

    ];



    public function order()
    {
        return $this->belongsTo(
            CustomerOrder::class,
            'customer_order_id'
        );
    }


}