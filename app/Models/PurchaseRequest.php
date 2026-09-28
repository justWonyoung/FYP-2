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

        'material_item',

        'quantity',

        'unit',

        'estimated_cost',

        'finance_status',

        'approval_status',

        'finance_remark',

        'admin_remark',

    ];



    /*
    |--------------------------------------------------------------------------
    | Purchase Request Items
    |--------------------------------------------------------------------------
    |
    | One purchase request can contain many materials
    |
    */

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
    |
    | Future upgrade:
    | supplier_id should replace supplier_name
    |
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
    |
    | Requested by staff/admin
    |
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