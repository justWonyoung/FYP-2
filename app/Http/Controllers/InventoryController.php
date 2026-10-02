<?php

namespace App\Http\Controllers;


use App\Models\Material;
use App\Models\MaterialReceiving;

use Illuminate\Http\Request;



class InventoryController extends Controller
{


    public function index()
    {

        $materials = Material::orderBy(
            'material_name',
            'asc'
        )
        ->get();


        return view(
            'inventory.index',
            compact('materials')
        );

    }


public function receivingHistory()
{

    $receivings = \App\Models\MaterialReceiving::orderBy(
        'created_at',
        'desc'
    )
    ->get();



    return view(
        'inventory.receiving_history',
        compact('receivings')
    );

}




    public function store(Request $request)
    {


        $validated = $request->validate([


            'material_name'
                => 'required|string|max:255',


            'supplier_name'
                => 'required|string|max:255',


            'quantity_received'
                => 'required|numeric|min:0',


            'unit'
                => 'required|string',


            'received_date'
                => 'required|date',


            'remarks'
                => 'nullable|string',


        ]);







        /*
        |--------------------------------------------------------------------------
        | FIND EXISTING MATERIAL
        |--------------------------------------------------------------------------
        */


        $material = Material::where(
            'material_name',
            $request->material_name
        )
        ->first();








        /*
        |--------------------------------------------------------------------------
        | UPDATE STOCK
        |--------------------------------------------------------------------------
        */


        if($material){


            $material->current_stock +=
                $request->quantity_received;


            $material->save();



        }

        else {


            Material::create([

                'material_name'
                    => $request->material_name,


                'material_type'
                    => 'Raw Material',


                'unit'
                    => $request->unit,


                'current_stock'
                    => $request->quantity_received,


                'minimum_stock'
                    => 10,


            ]);

        }








        /*
        |--------------------------------------------------------------------------
        | SAVE RECEIVING HISTORY
        |--------------------------------------------------------------------------
        */


        MaterialReceiving::create([


            'material_name'
                => $request->material_name,


            'supplier_name'
                => $request->supplier_name,


            'quantity_received'
                => $request->quantity_received,


            'unit'
                => $request->unit,


            'received_date'
                => $request->received_date,


            'remarks'
                => $request->remarks,

        ]);







        return redirect()
            ->back()
            ->with(
                'success',
                'Material received and stock updated successfully.'
            );


    }




}