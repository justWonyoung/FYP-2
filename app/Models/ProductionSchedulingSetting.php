<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionSchedulingSetting extends Model
{
    protected $fillable = [
        'daily_capacity',
        'unit',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'daily_capacity' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }
}
