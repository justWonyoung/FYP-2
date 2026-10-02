<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    use HasFactory;


    protected $primaryKey = 'purchase_request_id';



    protected $fillable = [

        'request_no',

        'customer_order_no',

        'supplier_name',

        'requested_by',

        'material_item',

        'quantity',

        'received_quantity',

        'unit',

        'estimated_cost',

        'finance_status',

        'approval_status',

        'delivery_status',

        'finance_remark',

        'admin_remark',

    ];



    /*
    |--------------------------------------------------------------------------
    | Purchase Request Items
    |--------------------------------------------------------------------------
    */

    public function staff()
    {
        return $this->belongsTo(
            User::class,
            'requested_by',
            'user_id'
        );
    }



    public function items()
    {
        return $this->hasMany(
            PurchaseRequestItem::class,
            'purchase_request_id',
            'purchase_request_id'
        );
    }




    /*
    |--------------------------------------------------------------------------
    | Supplier Relationship
    |--------------------------------------------------------------------------
    */

    public function supplier()
    {
        return $this->belongsTo(
            Supplier::class,
            'supplier_id',
            'id'
        );
    }





    /*
    |--------------------------------------------------------------------------
    | User Relationship
    |--------------------------------------------------------------------------
    */

    public function requester()
    {
        return $this->belongsTo(
            User::class,
            'requested_by',
            'id'
        );
    }


}