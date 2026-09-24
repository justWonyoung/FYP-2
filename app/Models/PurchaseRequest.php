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
}