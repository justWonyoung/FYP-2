<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialReceiving extends Model
{
    use HasFactory;

    protected $primaryKey = 'receiving_id';

    protected $fillable = [
        'purchase_request_id',
        'material_name',
        'supplier_name',
        'quantity_received',
        'unit',
        'received_date',
        'remarks',
    ];
}