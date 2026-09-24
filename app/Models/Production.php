<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    use HasFactory;

    protected $primaryKey = 'production_id';

    protected $fillable = [
        'batch_number',
        'customer_order_no',
        'product_name',
        'planned_output',
        'actual_output',
        'unit',
        'status',
        'start_date',
        'completion_date',
    ];

    public function usages()
    {
        return $this->hasMany(MaterialUsage::class, 'production_id', 'production_id');
    }

    public function wastes()
    {
        return $this->hasMany(MaterialWaste::class, 'production_id', 'production_id');
    }
}