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




    /*
    |--------------------------------------------------------------------------
    | Material Usage Relationship
    |--------------------------------------------------------------------------
    */

    public function usages()
    {
        return $this->hasMany(
            MaterialUsage::class,
            'production_id',
            'production_id'
        );
    }




    /*
    |--------------------------------------------------------------------------
    | Material Waste Relationship
    |--------------------------------------------------------------------------
    */

    public function wastes()
    {
        return $this->hasMany(
            MaterialWaste::class,
            'production_id',
            'production_id'
        );
    }





    /*
    |--------------------------------------------------------------------------
    | Production Yield Relationship
    |--------------------------------------------------------------------------
    |
    | One production batch has one yield calculation
    |
    */

    public function yield()
    {
        return $this->hasOne(
            ProductionYield::class,
            'production_id',
            'production_id'
        );
    }



}