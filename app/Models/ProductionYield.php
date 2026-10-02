<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;



class ProductionYield extends Model
{


    protected $table = 'production_yields';



    protected $primaryKey = 'id';



    protected $fillable = [


        'production_id',


        'planned_quantity',


        'actual_quantity',


        'yield_percentage',


    ];





    public function production()
    {


        return $this->belongsTo(

            Production::class,

            'production_id',

            'production_id'

        );


    }



}