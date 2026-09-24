<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Material;
use App\Models\MaterialReceiving;
use App\Models\PurchaseRequest;

class InventoryController extends Controller
{
    // View Inventory List
    public function index()
    {
        $materials = Material::latest()->get();
        return view('inventory.index', compact('materials'));
    }

    // View Material Receiving Form
    public function createReceiving()
    {
        $approvedPRs = PurchaseRequest::where('approval_status', 'approved')->get();
        return view('inventory.receive_material', compact('approvedPRs'));
    }

    // Store Receiving & Auto-Update Stock
    public function storeReceiving(Request $request)
    {
        $request->validate([
            'material_name' => 'required|string',
            'supplier_name' => 'required|string',
            'quantity_received' => 'required|numeric|min:0.01',
            'unit' => 'required|string',
            'received_date' => 'required|date',
            'material_type' => 'required|string',
        ]);

        // 1. Create Receiving Record
        MaterialReceiving::create([
            'purchase_request_id' => $request->purchase_request_id ?? null,
            'material_name' => $request->material_name,
            'supplier_name' => $request->supplier_name,
            'quantity_received' => $request->quantity_received,
            'unit' => $request->unit,
            'received_date' => $request->received_date,
            'remarks' => $request->remarks,
        ]);

        // 2. Auto-Update or Create Material Stock in Inventory
        $material = Material::where('material_name', $request->material_name)->first();

        if ($material) {
            $material->increment('current_stock', $request->quantity_received);
        } else {
            Material::create([
                'material_name' => $request->material_name,
                'material_type' => $request->material_type,
                'unit' => $request->unit,
                'current_stock' => $request->quantity_received,
                'minimum_stock' => 10.00,
            ]);
        }

        return redirect()->route('inventory.index')->with('success', 'Material received! Stock level updated automatically.');
    }
}