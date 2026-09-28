<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class CustomerOrder extends Model
{

    use HasFactory;


    protected $fillable = [

        'customer_id',
        'order_no',
        'order_date',
        'status',
        'total_amount'

    ];


    public function customer()
    {
        return $this->belongsTo(
            Customer::class
        );
    }



    public function items()
    {
        return $this->hasMany(
            CustomerOrderItem::class
        );
    }


}