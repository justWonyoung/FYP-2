<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Models\Material;
use Illuminate\Support\Facades\Auth;


class PurchaseRequestController extends Controller
{


    /*
    |--------------------------------------------------------------------------
    | STAFF MODULE
    |--------------------------------------------------------------------------
    */


    // Show Create Purchase Request Form
    public function create()
    {
        return view('staff.create_pr');
    }



    // Store Purchase Request
    public function store(Request $request)
    {

        $request->validate([

            'customer_order_no' => 'required|string',

            'supplier_name' => 'required|string',

            'material_item' => 'required|string',

            'quantity' => 'required|numeric',

            'unit' => 'required|string',

            'estimated_cost' => 'required|numeric',

        ]);



        $prCount = PurchaseRequest::count() + 1;


        $requestNo = 'PR-' .
            str_pad(
                $prCount,
                3,
                '0',
                STR_PAD_LEFT
            );



        PurchaseRequest::create([


            'request_no' => $requestNo,

            'customer_order_no' =>
                $request->customer_order_no,


            'supplier_name' =>
                $request->supplier_name,


            'material_item' =>
                $request->material_item,


            'quantity' =>
                $request->quantity,


            'unit' =>
                $request->unit,


            'estimated_cost' =>
                $request->estimated_cost,


            'finance_status' =>
                'pending',


            'approval_status' =>
                'pending',


            'requested_by' =>
                Auth::id(),


        ]);



        return redirect()

            ->route('staff.dashboard')

            ->with(
                'success',
                'Purchase Request '.$requestNo.' submitted successfully!'
            );

    }






    /*
    |--------------------------------------------------------------------------
    | FINANCE MODULE
    |--------------------------------------------------------------------------
    */


    // Display Purchase Requests For Finance

// Show one Purchase Request for Finance Review
public function showFinanceReview($id)
{
    $pr = PurchaseRequest::findOrFail($id);

    // Only pending requests should normally be reviewed
    if ($pr->finance_status !== 'pending') {
        return redirect()
            ->route('finance.dashboard')
            ->with('error', 'This Purchase Request has already been reviewed.');
    }

    return view(
        'finance.review_pr',
        compact('pr')
    );
}



    // Finance Approve

 // Finance Approve
public function financeApprove($id)
{
    $pr = PurchaseRequest::findOrFail($id);

    $pr->update([
        'finance_status' => 'approved',
    ]);

    return redirect()
        ->route('finance.dashboard')
        ->with(
            'success',
            'Purchase Request ' . $pr->request_no . ' approved by Finance and forwarded to Admin.'
        );
}





    // Finance Reject

   // Finance Reject
public function financeReject($id)
{
    $pr = PurchaseRequest::findOrFail($id);

    $pr->update([
        'finance_status' => 'rejected',
    ]);

    return redirect()
        ->route('finance.dashboard')
        ->with(
            'success',
            'Purchase Request ' . $pr->request_no . ' rejected by Finance.'
        );
}






    /*
    |--------------------------------------------------------------------------
    | ADMIN MODULE
    |--------------------------------------------------------------------------
    */

    // Show one Purchase Request for Admin Review

public function adminReview($id)
{
    $pr = PurchaseRequest::findOrFail($id);

    return view(
        'admin.review_pr',
        compact('pr')
    );
}

    // Display Requests For Admin

// Display Purchase Requests Waiting For Admin Approval

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





    // Admin Approve

    public function approve($id)
    {

        $pr = PurchaseRequest::findOrFail($id);



        $pr->update([

            'approval_status'
                => 'approved'

        ]);



        // Update Inventory

        $material = Material::where(

            'material_name',

            $pr->material_item

        )->first();



        if($material){


            $material->increment(

                'current_stock',

                $pr->quantity

            );


        }else{


            Material::create([


                'material_name'
                    => $pr->material_item,


                'material_type'
                    => 'Raw Material',


                'unit'
                    => $pr->unit,


                'current_stock'
                    => $pr->quantity,


                'minimum_stock'
                    => 10,


            ]);

        }



        return redirect()

            ->route('admin.purchase.requests')

            ->with(

                'success',

                'Purchase Request approved and inventory updated'

            );

    }





    // Admin Reject

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