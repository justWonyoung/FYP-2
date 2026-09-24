<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Models\Material;

class PurchaseRequestController extends Controller
{
    // Staff Functions
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
            'quantity' => 'required|numeric',
            'unit' => 'required|string',
            'estimated_cost' => 'required|numeric',
        ]);

        $prCount = PurchaseRequest::count() + 1;
        $requestNo = 'PR-' . str_pad($prCount, 3, '0', STR_PAD_LEFT);

        PurchaseRequest::create([
            'request_no' => $requestNo,
            'customer_order_no' => $request->customer_order_no,
            'supplier_name' => $request->supplier_name,
            'material_item' => $request->material_item,
            'quantity' => $request->quantity,
            'unit' => $request->unit,
            'estimated_cost' => $request->estimated_cost,
            'finance_status' => 'pending',
            'approval_status' => 'pending',
        ]);

        return redirect()->route('staff.dashboard')->with('success', 'Purchase Request ' . $requestNo . ' submitted successfully!');
    }

    // Finance Functions
    public function showFinanceReview($id)
    {
        $pr = PurchaseRequest::findOrFail($id);
        return view('finance.review_pr', compact('pr'));
    }

    public function financeReview(Request $request, $id)
    {
        $pr = PurchaseRequest::findOrFail($id);
        
        $request->validate([
            'finance_status' => 'required|in:approved,rejected',
            'finance_remark' => 'nullable|string',
        ]);

        $pr->update([
            'finance_status' => $request->finance_status,
            'finance_remark' => $request->finance_remark,
        ]);

        return redirect()->route('finance.dashboard')->with('success', 'PR ' . $pr->request_no . ' reviewed by Finance!');
    }

    // Admin Functions
    public function showAdminApprove($id)
    {
        $pr = PurchaseRequest::findOrFail($id);
        return view('admin.approve_pr', compact('pr'));
    }

    public function adminApprove(Request $request, $id)
    {
        $pr = PurchaseRequest::findOrFail($id);

        $request->validate([
            'approval_status' => 'required|in:approved,rejected',
            'admin_remark' => 'nullable|string',
        ]);

        $pr->update([
            'approval_status' => $request->approval_status,
            'admin_remark' => $request->admin_remark,
        ]);

        // AUTOMATIC INVENTORY INTEGRATION:
        // If Admin grants Final Approval, automatically inject/update item in Inventory!
        if ($request->approval_status === 'approved') {
            $material = Material::where('material_name', $pr->material_item)->first();

            if ($material) {
                $material->increment('current_stock', $pr->quantity);
            } else {
                Material::create([
                    'material_name' => $pr->material_item,
                    'material_type' => 'Raw Material',
                    'unit'          => $pr->unit,
                    'current_stock' => $pr->quantity,
                    'minimum_stock' => 10.00,
                ]);
            }
        }

        return redirect()->route('admin.dashboard')->with('success', 'PR ' . $pr->request_no . ' approved! Material automatically added to Inventory.');
    }
}