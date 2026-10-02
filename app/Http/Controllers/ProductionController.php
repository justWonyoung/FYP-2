<?php

namespace App\Http\Controllers;


use App\Models\Production;
use App\Models\Material;
use App\Models\MaterialUsage;
use App\Models\MaterialWaste;
use App\Models\ProductionYield;


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


        return view(

            'production.create'

        );


    }








    /*
    |--------------------------------------------------------------------------
    | STORE PRODUCTION BATCH
    |--------------------------------------------------------------------------
    */


    public function store(Request $request)
    {



        $request->validate([



            'batch_number'

            =>

            'required|unique:productions,batch_number',




            'customer_order_no'

            =>

            'required',





            'product_name'

            =>

            'required',





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







        Production::create([



            'batch_number'

            =>

            $request->batch_number,




            'customer_order_no'

            =>

            $request->customer_order_no,




            'product_name'

            =>

            $request->product_name,




            'planned_output'

            =>

            $request->planned_output,




            'actual_output'

            =>

            0,




            'unit'

            =>

            $request->unit,




            'status'

            =>

            'in_progress',




            'start_date'

            =>

            $request->start_date,



        ]);







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









        return redirect()

        ->route(

            'production.index'

        )

        ->with(

            'success',

            'Production completed and yield recorded successfully.'

        );



    }






}