<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class PurchaseRequestController extends Controller
{


    /*
    |--------------------------------------------------------------------------
    | STAFF MODULE
    |--------------------------------------------------------------------------
    */


    public function create()
    {
        return view('staff.create_pr');
    }




    public function store(Request $request)
    {

        $request->validate([

            'customer_order_no' => 'required|string',

            'supplier_name' => 'required|string',

            'material_item' => 'required|string',

            'quantity' => 'required|numeric|min:1',

            'unit' => 'required|string',

            'estimated_cost' => 'required|numeric|min:0',

        ]);



        DB::beginTransaction();



        try
        {


            $lastRequest = PurchaseRequest::latest(
                'purchase_request_id'
            )->first();



            if($lastRequest)
            {

                $number = intval(
                    str_replace(
                        'PR-',
                        '',
                        $lastRequest->request_no
                    )
                ) + 1;

            }
            else
            {

                $number = 1;

            }



            $requestNo =
                'PR-' .
                str_pad(
                    $number,
                    3,
                    '0',
                    STR_PAD_LEFT
                );





            PurchaseRequest::create([


                'request_no'
                    => $requestNo,


                'customer_order_no'
                    => $request->customer_order_no,


                'supplier_name'
                    => $request->supplier_name,


                'material_item'
                    => $request->material_item,


                'quantity'
                    => $request->quantity,

                'received_quantity'
                    => 0,


                'delivery_status'
                  => 'pending',

                'unit'
                    => $request->unit,


                'estimated_cost'
                    => $request->estimated_cost,


                'finance_status'
                    => 'pending',


'approval_status'
    => 'pending',

'requested_by'
    => Auth::id(),


            ]);




            DB::commit();



            return redirect()

                ->route('staff.dashboard')

                ->with(

                    'success',

                    'Purchase Request '.$requestNo.' submitted successfully!'

                );



        }
        catch(\Exception $e)
        {

            DB::rollBack();


            return back()

                ->with(

                    'error',

                    'Failed to submit Purchase Request: '.$e->getMessage()

                );


        }


    }









    /*
    |--------------------------------------------------------------------------
    | FINANCE MODULE
    |--------------------------------------------------------------------------
    */


    public function showFinanceReview($id)
    {


        $pr = PurchaseRequest::findOrFail($id);



        if($pr->finance_status !== 'pending')
        {

            return redirect()

                ->route('finance.dashboard')

                ->with(

                    'error',

                    'This Purchase Request has already been reviewed.'

                );

        }



        return view(

            'finance.review_pr',

            compact('pr')

        );


    }







    public function financeApprove($id)
    {


        $pr = PurchaseRequest::findOrFail($id);



        $pr->update([


            'finance_status'
                => 'approved',


            'finance_remark'
                => 'Approved by Finance'


        ]);





        return redirect()

            ->route('finance.dashboard')

            ->with(

                'success',

                'Purchase Request forwarded to Admin approval.'

            );


    }







    public function financeReject($id)
    {


        $pr = PurchaseRequest::findOrFail($id);



        $pr->update([


            'finance_status'
                => 'rejected'


        ]);





        return redirect()

            ->route('finance.dashboard')

            ->with(

                'success',

                'Purchase Request '.$pr->request_no.' rejected by Finance.'

            );


    }










    /*
    |--------------------------------------------------------------------------
    | ADMIN MODULE
    |--------------------------------------------------------------------------
    */



    public function adminReview($id)
    {


        $pr = PurchaseRequest::findOrFail($id);



        return view(

            'admin.review_pr',

            compact('pr')

        );


    }









    public function index()
    {


        $requests = PurchaseRequest::where(

            'finance_status',

            'approved'

        )

        ->where(

            'approval_status',

            'pending'

        )

        ->latest()

        ->get();



        return view(

            'admin.approve_pr',

            compact('requests')

        );


    }









    /*
    |--------------------------------------------------------------------------
    | ADMIN FINAL APPROVAL
    |--------------------------------------------------------------------------
    |
    | Finance approved
    |        ↓
    | Admin approved
    |        ↓
    | Waiting for supplier delivery
    |        ↓
    | Inventory receiving updates stock
    |
    |--------------------------------------------------------------------------
    */


    public function approve($id)
    {


        $pr = PurchaseRequest::findOrFail($id);



        $pr->update([


            'approval_status'
                => 'approved'


        ]);






        return redirect()

            ->route('admin.dashboard')

            ->with(

                'success',

                'Purchase Request approved. Waiting for material receiving.'

            );


    }









    public function reject($id)
    {


        $pr = PurchaseRequest::findOrFail($id);



        $pr->update([


            'approval_status'
                => 'rejected'


        ]);





        return redirect()

            ->route('admin.purchase.requests')

            ->with(

                'success',

                'Purchase Request rejected'

            );


    }




}