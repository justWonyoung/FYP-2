<?php

namespace App\Http\Controllers;


use App\Models\Production;
use App\Models\CustomerOrder;
use App\Models\Material;
use App\Models\MaterialUsage;
use App\Models\MaterialWaste;
use App\Models\ProductionYield;
use App\Models\ActivityLog;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;



class ProductionController extends Controller
{



    /*
    |--------------------------------------------------------------------------
    | PRODUCTION LIST
    |--------------------------------------------------------------------------
    */


    public function index()
    {


        $batches = Production::with([

            'wastes'

        ])

        ->orderBy(

            'created_at',

            'desc'

        )

        ->get();





        return view(

            'production.index',

            compact(

                'batches'

            )

        );

    }






    /*
    |--------------------------------------------------------------------------
    | CREATE PRODUCTION BATCH
    |--------------------------------------------------------------------------
    */


    public function create()
    {
        $customerOrders = CustomerOrder::query()
            ->with(['customer:id,customer_name', 'items:id,customer_order_id,product_name,quantity'])
            ->where('status', '!=', 'cancelled')
            ->orderByDesc('order_date')
            ->orderByDesc('id')
            ->get(['id', 'customer_id', 'order_no', 'order_date', 'status']);

        return view('production.create', compact('customerOrders'));


    }








    /*
    |--------------------------------------------------------------------------
    | STORE PRODUCTION BATCH
    |--------------------------------------------------------------------------
    */


    public function store(Request $request)
    {



        $validated = $request->validate([



            'batch_number'

            =>

            'required|unique:productions,batch_number',




            'customer_order_no' => ['required', 'string', 'max:255'],





            'product_name' => ['required', 'string', 'max:255'],





            'planned_output'

            =>

            'required|numeric',





            'unit'

            =>

            'required',





            'start_date'

            =>

            'required|date',



        ]);

        $customerOrder = CustomerOrder::query()
            ->with('items:id,customer_order_id,product_name,quantity')
            ->where('order_no', $validated['customer_order_no'])
            ->where('status', '!=', 'cancelled')
            ->first();

        if (! $customerOrder) {
            return back()->withErrors([
                'customer_order_no' => 'The selected customer order does not exist or is cancelled.',
            ])->withInput();
        }

        if (! $customerOrder->items->contains('product_name', $validated['product_name'])) {
            return back()->withErrors([
                'product_name' => 'The selected product does not belong to the selected customer order.',
            ])->withInput();
        }







        $production = Production::create([



            'batch_number' => $validated['batch_number'],




            'customer_order_no' => $validated['customer_order_no'],




            'product_name' => $validated['product_name'],




            'planned_output' => $validated['planned_output'],




            'actual_output'

            =>

            0,




            'unit' => $validated['unit'],




            'status'

            =>

            'in_progress',




            'start_date' => $validated['start_date'],



        ]);

        ActivityLog::record(
            $request->user(),
            'created',
            'Production',
            "Created production batch {$production->batch_number}."
        );







        return redirect()

        ->route(

            'production.index'

        )

        ->with(

            'success',

            'Production batch created successfully.'

        );



    }









    /*
    |--------------------------------------------------------------------------
    | MATERIAL / WASTE LOG PAGE
    |--------------------------------------------------------------------------
    */


    public function log($id)
    {



        $batch = Production::with([


            'materialUsages.material',


            'wastes.material'


        ])

        ->findOrFail($id);







        $materials = Material::orderBy(

            'material_name'

        )

        ->get();







        return view(

            'production.log',

            compact(

                'batch',

                'materials'

            )

        );



    }









    /*
    |--------------------------------------------------------------------------
    | SAVE MATERIAL USAGE
    |--------------------------------------------------------------------------
    */


    public function storeUsage(Request $request,$id)
    {



        $request->validate([



            'material_id'

            =>

            'required',




            'quantity_used'

            =>

            'required|numeric|min:0.01',



        ]);







        DB::transaction(function() use ($request,$id){





            $material = Material::findOrFail(

                $request->material_id

            );







            if(

                $material->current_stock

                <

                $request->quantity_used

            )

            {



                throw new \Exception(

                    'Insufficient material stock.'

                );


            }







            MaterialUsage::create([




                'production_id'

                =>

                $id,





                'material_id'

                =>

                $request->material_id,





                'quantity_used'

                =>

                $request->quantity_used,



            ]);








            $material->current_stock -=

            $request->quantity_used;





            $material->save();




        });








        return back()

        ->with(

            'success',

            'Material usage recorded successfully.'

        );



    }









    /*
    |--------------------------------------------------------------------------
    | SAVE MATERIAL WASTE
    |--------------------------------------------------------------------------
    */


    public function storeWaste(Request $request,$id)
    {



        $request->validate([




            'material_id'

            =>

            'required',




            'quantity_wasted'

            =>

            'required|numeric|min:0.01',





            'waste_reason'

            =>

            'nullable|string',



        ]);







        MaterialWaste::create([




            'production_id'

            =>

            $id,





            'material_id'

            =>

            $request->material_id,





            'quantity_wasted'

            =>

            $request->quantity_wasted,





            'waste_reason'

            =>

            $request->waste_reason,



        ]);








        return back()

        ->with(

            'success',

            'Material waste recorded successfully.'

        );



    }









    /*
    |--------------------------------------------------------------------------
    | UPDATE ACTUAL OUTPUT
    |--------------------------------------------------------------------------
    */


    public function updateOutput(Request $request,$id)
    {



        $request->validate([



            'actual_output'

            =>

            'required|numeric|min:0',



        ]);







        $production = Production::findOrFail($id);





        $production->actual_output =

        $request->actual_output;





        $production->save();








        return back()

        ->with(

            'success',

            'Actual output updated successfully.'

        );



    }









    /*
    |--------------------------------------------------------------------------
    | COMPLETE PRODUCTION + SAVE YIELD
    |--------------------------------------------------------------------------
    */


    public function complete($id)
    {



        $production = Production::findOrFail($id);







        DB::transaction(function() use ($production){





            $yieldPercentage = 0;







            if(

                $production->planned_output > 0

            )

            {



                $yieldPercentage =


                (

                    $production->actual_output

                    /

                    $production->planned_output

                )

                *

                100;



            }










            $production->status =

            'completed';







            $production->completion_date =

            now();







            $production->save();









            ProductionYield::updateOrCreate(




                [



                    'production_id'

                    =>

                    $production->production_id



                ],





                [



                    'planned_quantity'

                    =>

                    $production->planned_output,





                    'actual_quantity'

                    =>

                    $production->actual_output,





                    'yield_percentage'

                    =>

                    $yieldPercentage,



                ]



            );





        });

        ActivityLog::record(
            request()->user(),
            'completed',
            'Production',
            "Completed production batch {$production->batch_number}."
        );









        return redirect()

        ->route(

            'production.index'

        )

        ->with(

            'success',

            'Production completed and yield recorded successfully.'

        );



    }

/*
|--------------------------------------------------------------------------
| PRODUCTION REPORT
|--------------------------------------------------------------------------
*/

public function report()
{

    $batches = Production::with([
        'wastes',
        'materialUsages.material'
    ])
    ->orderBy(
        'created_at',
        'desc'
    )
    ->get();



    $totalBatches = $batches->count();



    $completedBatches = $batches
        ->where('status','completed')
        ->count();



    $inProgress = $batches
        ->where('status','in_progress')
        ->count();



    $totalPlanned = $batches
        ->sum('planned_output');



    $totalActual = $batches
        ->sum('actual_output');



    $yield = 0;


    if($totalPlanned > 0)
    {

        $yield = ($totalActual / $totalPlanned) * 100;

    }



    return view(
        'production.report',
        compact(
            'batches',
            'totalBatches',
            'completedBatches',
            'inProgress',
            'totalPlanned',
            'totalActual',
            'yield'
        )
    );

}




}
