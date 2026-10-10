<?php

namespace App\Http\Controllers;

use App\Models\CustomerOrder;
use App\Models\ActivityLog;
use App\Models\PurchaseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PurchaseRequestController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | STAFF MODULE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $customerOrders = CustomerOrder::query()
            ->where('status', '!=', 'cancelled')
            ->orderByDesc('order_date')
            ->orderByDesc('id')
            ->get(['order_no', 'customer_id', 'order_date', 'status']);

        $customerOrders->load('customer:id,customer_name');

        return view('staff.create_pr', compact('customerOrders'));
    }

    public function store(Request $request)
    {

        $request->validate([

            'customer_order_no' => [
                'required',
                'string',
                Rule::exists('customer_orders', 'order_no')->where(
                    fn ($query) => $query->where('status', '!=', 'cancelled')
                ),
            ],

            'supplier_name' => 'required|string',

            'material_item' => 'required|string',

            'quantity' => 'required|numeric|min:1',

            'unit' => 'required|string',

            'estimated_cost' => 'required|numeric|min:0',

        ]);

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | Generate Purchase Request Number
            |--------------------------------------------------------------------------
            */

            $lastRequest = PurchaseRequest::orderBy(
                'purchase_request_id',
                'desc'
            )
                ->first();

            if ($lastRequest) {

                $number = intval(
                    str_replace(
                        'PR-',
                        '',
                        $lastRequest->request_no
                    )
                ) + 1;

            } else {

                $number = 1;

            }

            $requestNo =
                'PR-'.
                str_pad(
                    $number,
                    3,
                    '0',
                    STR_PAD_LEFT
                );

            /*
            |--------------------------------------------------------------------------
            | Create Purchase Request
            |--------------------------------------------------------------------------
            */

            $purchaseRequest = PurchaseRequest::create([

                'request_no' => $requestNo,

                'customer_order_no' => $request->customer_order_no,

                'supplier_name' => $request->supplier_name,

                'requested_by' => Auth::id(),

                'material_item' => $request->material_item,

                'quantity' => $request->quantity,

                'received_quantity' => 0,

                'delivery_status' => 'pending',

                'unit' => $request->unit,

                'estimated_cost' => $request->estimated_cost,

                'finance_status' => 'pending',

                'approval_status' => 'pending',

            ]);

            DB::commit();

            ActivityLog::record(
                $request->user(),
                'created',
                'Purchase Request',
                "Submitted purchase request {$purchaseRequest->request_no}."
            );

            return redirect()

                ->route('staff.dashboard')

                ->with(
                    'success',
                    'Purchase Request '.$requestNo.' submitted successfully!'
                );

        } catch (\Exception $e) {

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

        $pr = PurchaseRequest::with('staff')
            ->findOrFail($id);

        if ($pr->finance_status !== 'pending') {

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

        if ($pr->finance_status !== 'pending') {

            return redirect()

                ->route('finance.dashboard');

        }

        $pr->update([

            'finance_status' => 'approved',

            'finance_remark' => 'Approved by Finance',

        ]);

        ActivityLog::record(request()->user(), 'approved', 'Purchase Request', "Finance approved {$pr->request_no}.");

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

            'finance_status' => 'rejected',

            'finance_remark' => 'Rejected by Finance',

        ]);

        ActivityLog::record(request()->user(), 'rejected', 'Purchase Request', "Finance rejected {$pr->request_no}.");

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

        $pr = PurchaseRequest::with('staff')
            ->findOrFail($id);

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
    */

    public function approve($id)
    {

        $pr = PurchaseRequest::findOrFail($id);

        if ($pr->approval_status !== 'pending') {

            return redirect()

                ->route('admin.dashboard');

        }

        $pr->update([

            'approval_status' => 'approved',

            'admin_remark' => 'Approved by Admin',

        ]);

        ActivityLog::record(request()->user(), 'approved', 'Purchase Request', "Admin approved {$pr->request_no}.");

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

            'approval_status' => 'rejected',

            'admin_remark' => 'Rejected by Admin',

        ]);

        ActivityLog::record(request()->user(), 'rejected', 'Purchase Request', "Admin rejected {$pr->request_no}.");

        return redirect()

            ->route('admin.purchase.requests')

            ->with(
                'success',
                'Purchase Request rejected.'
            );

    }
}
