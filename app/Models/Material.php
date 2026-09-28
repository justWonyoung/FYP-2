<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;


    protected $primaryKey = 'material_id';


    protected $fillable = [
        'material_name',
        'material_type',
        'unit',
        'current_stock',
        'minimum_stock',
    ];



    /*
    |--------------------------------------------------------------------------
    | Relationship
    |--------------------------------------------------------------------------
    |
    | One material can appear in many purchase request items
    |
    */

    public function purchaseRequestItems()
    {
        return $this->hasMany(
            PurchaseRequestItem::class,
            'material_id',
            'material_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Existing / Future Relationships
    |--------------------------------------------------------------------------
    */

    public function usages()
    {
        return $this->hasMany(
            MaterialUsage::class,
            'material_id',
            'material_id'
        );
    }


    public function wastes()
    {
        return $this->hasMany(
            MaterialWaste::class,
            'material_id',
            'material_id'
        );
    }


}