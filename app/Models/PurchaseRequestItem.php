<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class PurchaseRequestItem extends Model
{

    use HasFactory;


    protected $fillable = [

        'purchase_request_id',
        'material_id',
        'quantity',
        'unit_price',
        'subtotal'

    ];



    public function purchaseRequest()
    {
        return $this->belongsTo(
            PurchaseRequest::class
        );
    }



    public function material()
    {
        return $this->belongsTo(
            Material::class
        );
    }

}