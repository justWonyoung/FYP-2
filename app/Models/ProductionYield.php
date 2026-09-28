<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ProductionYield extends Model
{

    use HasFactory;


    protected $fillable = [

        'production_id',
        'planned_quantity',
        'actual_quantity',
        'yield_percentage'

    ];



    public function production()
    {
        return $this->belongsTo(
            Production::class
        );
    }

}