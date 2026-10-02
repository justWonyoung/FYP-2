<?php

namespace App\Http\Controllers;


use App\Models\Material;
use App\Models\MaterialReceiving;
use App\Models\PurchaseRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;



class InventoryController extends Controller
{


    /*
    |--------------------------------------------------------------------------
    | INVENTORY MAIN PAGE
    |--------------------------------------------------------------------------
    */


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









    /*
    |--------------------------------------------------------------------------
    | RECEIVE MATERIAL PAGE
    |--------------------------------------------------------------------------
    |
    | Show approved Purchase Requests waiting for delivery.
    |
    | Flow:
    |
    | Staff PR
    |      ↓
    | Finance approve
    |      ↓
    | Admin approve
    |      ↓
    | Warehouse receive
    |      ↓
    | Inventory update
    |
    |--------------------------------------------------------------------------
    */


    public function receive()
{

    $approvedPRs = PurchaseRequest::where(
            'approval_status',
            'approved'
        )
        ->whereIn(
            'delivery_status',
            [
                'pending',
                'partial'
            ]
        )
        ->orderBy(
            'created_at',
            'desc'
        )
        ->get();



    return view(
        'inventory.receive_material',
        compact('approvedPRs')
    );

}









    /*
    |--------------------------------------------------------------------------
    | GET PURCHASE REQUEST DETAILS
    |--------------------------------------------------------------------------
    |
    | Used by JavaScript when user selects PR.
    |
    | Example:
    |
    | Select PR-008
    |
    | Return:
    |
    | Material: Shea Butter
    | Supplier: Azalea Chemical
    | Quantity: 10 kg
    |
    |--------------------------------------------------------------------------
    */


    public function getPRDetails($id)
{


    $pr = PurchaseRequest::findOrFail($id);



    return response()->json([


        'material'
            => $pr->material_item,


        'supplier'
            => $pr->supplier_name,


        'ordered_quantity'
            => $pr->quantity,


        'received_quantity'
            => $pr->received_quantity,


        'remaining_quantity'
            => $pr->quantity - $pr->received_quantity,


        'unit'
            => $pr->unit,


    ]);


}









    /*
    |--------------------------------------------------------------------------
    | STORE RECEIVED MATERIAL
    |--------------------------------------------------------------------------
    */


    public function store(Request $request)
{


    $validated = $request->validate([


        'purchase_request_id'
            => 'required|exists:purchase_requests,purchase_request_id',


        'quantity_received'
            => 'required|numeric|min:0.01',


        'received_date'
            => 'required|date',


        'remarks'
            => 'nullable|string',


    ]);





    DB::beginTransaction();



    try
    {


        $pr = PurchaseRequest::findOrFail(
            $request->purchase_request_id
        );





        /*
        |--------------------------------------------------------------------------
        | CHECK REMAINING QUANTITY
        |--------------------------------------------------------------------------
        */


        $remaining = 
            $pr->quantity - $pr->received_quantity;



        if($request->quantity_received > $remaining)
        {


            return back()

                ->with(
                    'error',
                    'Cannot receive more than remaining quantity. Remaining: '
                    .$remaining.' '.$pr->unit
                );


        }







        /*
        |--------------------------------------------------------------------------
        | UPDATE PR RECEIVING STATUS
        |--------------------------------------------------------------------------
        */


        $newReceived = 
            $pr->received_quantity 
            +
            $request->quantity_received;




        if($newReceived >= $pr->quantity)
        {


            $status = 'received';


        }
        else
        {


            $status = 'partial';


        }





        $pr->update([


            'received_quantity'
                => $newReceived,


            'delivery_status'
                => $status,


        ]);







        /*
        |--------------------------------------------------------------------------
        | UPDATE INVENTORY
        |--------------------------------------------------------------------------
        */


        $material = Material::where(
                'material_name',
                $pr->material_item
            )
            ->first();





        if($material)
        {


            $material->current_stock += 
                $request->quantity_received;


            $material->save();


        }
        else
        {


            Material::create([


                'material_name'
                    => $pr->material_item,


                'material_type'
                    => 'Raw Material',


                'unit'
                    => $pr->unit,


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


            'purchase_request_id'
                => $pr->purchase_request_id,


            'material_name'
                => $pr->material_item,


            'supplier_name'
                => $pr->supplier_name,


            'quantity_received'
                => $request->quantity_received,


            'unit'
                => $pr->unit,


            'received_date'
                => $request->received_date,


            'remarks'
                => $request->remarks,


        ]);







        DB::commit();





        return redirect()

            ->route('inventory.index')

            ->with(
                'success',
                'Material received successfully.'
            );


    }
    catch(\Exception $e)
    {


        DB::rollBack();



        return back()

            ->with(
                'error',
                'Receiving failed: '.$e->getMessage()
            );


    }


}









    /*
    |--------------------------------------------------------------------------
    | RECEIVING HISTORY
    |--------------------------------------------------------------------------
    */


    public function receivingHistory()
    {


        $receivings = MaterialReceiving::orderBy(

            'created_at',

            'desc'

        )
        ->get();




        return view(

            'inventory.receiving_history',

            compact('receivings')

        );


    }









    /*
    |--------------------------------------------------------------------------
    | INVENTORY REPORT
    |--------------------------------------------------------------------------
    */


    public function report()
    {


        $materials = Material::orderBy(

            'material_name',

            'asc'

        )
        ->get();





        $totalMaterials = Material::count();





        $lowStock = Material::whereColumn(

            'current_stock',

            '<=',

            'minimum_stock'

        )
        ->count();





        $totalStock = Material::sum(

            'current_stock'

        );






        return view(

            'inventory.report',

            compact(

                'materials',

                'totalMaterials',

                'lowStock',

                'totalStock'

            )

        );


    }



}