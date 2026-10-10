<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [

        'supplier_name',
        'phone',
        'email',
        'address',

    ];

    public function purchaseRequests(): HasMany
    {
        return $this->hasMany(
            PurchaseRequest::class,
            'supplier_name',
            'supplier_name'
        );
    }

    public function isReferencedByPurchaseRequests(): bool
    {
        return $this->purchaseRequests()->exists();
    }
}
