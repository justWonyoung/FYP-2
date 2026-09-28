<?php

namespace App\Http\Controllers;

use App\Models\Production;
use Illuminate\Http\Request;

class ProductionController extends Controller
{
public function index()
{

    $batches = Production::with('wastes')
        ->orderBy(
            'created_at',
            'desc'
        )
        ->get();



    return view(
        'production.index',
        compact('batches')
    );

}
}