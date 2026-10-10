<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseRequest extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Primary Key
    |--------------------------------------------------------------------------
    */

    protected $primaryKey = 'purchase_request_id';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'request_no',

        'customer_order_no',

        'supplier_name',

        'requested_by',

        'material_item',

        'quantity',

        'received_quantity',

        'delivery_status',

        'unit',

        'estimated_cost',

        'finance_status',

        'approval_status',

        'finance_remark',

        'admin_remark',

    ];

    public function customerOrder(): BelongsTo
    {
        return $this->belongsTo(
            CustomerOrder::class,
            'customer_order_no',
            'order_no'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Staff / Requester Relationship
    |--------------------------------------------------------------------------
    |
    | purchase_requests.requested_by
    |              |
    |              v
    | users.user_id
    |
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

    /*
    |--------------------------------------------------------------------------
    | Purchase Request Items Relationship
    |--------------------------------------------------------------------------
    */

    public function items()
    {
        return $this->hasMany(
            PurchaseRequestItem::class,
            'purchase_request_id',
            'purchase_request_id'
        );
    }
}
