<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $primaryKey = 'expense_id';

    protected $fillable = [
        'expense_category',
        'description',
        'amount',
        'expense_date',
        'receipt_file',
    ];
}