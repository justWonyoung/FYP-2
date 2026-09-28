<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Customer extends Model
{
    use HasFactory;


    protected $fillable = [
        'customer_name',
        'phone',
        'email',
        'address'
    ];


    public function orders()
    {
        return $this->hasMany(CustomerOrder::class);
    }
}