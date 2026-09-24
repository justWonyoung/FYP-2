<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Production;
use App\Models\Material;
use App\Models\MaterialUsage;
use App\Models\MaterialWaste;

class ProductionController extends Controller
{
    // Production List
    public function index()
    {
        $batches = Production::with(['usages.material', 'wastes.material'])->latest()->get();
        return view('production.index', compact('batches'));
    }

    // Form to Create New Batch
    public function create()
    {
        return view('production.create');
    }

    // Store Batch
    public function store(Request $request)
    {
        $request->validate([
            'customer_order_no' => 'required|string',
            'product_name' => 'required|string',
            'planned_output' => 'required|numeric|min:1',
            'unit' => 'required|string',
            'start_date' => 'required|date',
        ]);

        $count = Production::count() + 1;
        $batchNo = 'BATCH-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        Production::create([
            'batch_number' => $batchNo,
            'customer_order_no' => $request->customer_order_no,
            'product_name' => $request->product_name,
            'planned_output' => $request->planned_output,
            'unit' => $request->unit,
            'start_date' => $request->start_date,
            'status' => 'in_progress',
        ]);

        return redirect()->route('production.index')->with('success', 'Production batch ' . $batchNo . ' created!');
    }

    // Form to Record Consumption & Waste
    public function showRecordForm($id)
    {
        $batch = Production::findOrFail($id);
        $materials = Material::where('current_stock', '>', 0)->get();
        return view('production.record_usage', compact('batch', 'materials'));
    }

    // Save Material Usage & Waste + Auto-Deduct Inventory
    public function storeRecordUsage(Request $request, $id)
    {
        $batch = Production::findOrFail($id);

        $request->validate([
            'material_id' => 'required|exists:materials,material_id',
            'quantity_used' => 'required|numeric|min:0.01',
            'quantity_wasted' => 'nullable|numeric|min:0',
            'waste_reason' => 'nullable|string',
            'actual_output' => 'nullable|numeric|min:0',
            'status' => 'required|in:in_progress,completed',
        ]);

        $material = Material::findOrFail($request->material_id);

        // Check if enough stock exists
        if ($material->current_stock < $request->quantity_used) {
            return back()->withErrors(['quantity_used' => 'Not enough stock! Current stock for ' . $material->material_name . ' is ' . $material->current_stock . ' ' . $material->unit]);
        }

        // 1. Record Usage
        MaterialUsage::create([
            'production_id' => $batch->production_id,
            'material_id' => $material->material_id,
            'quantity_used' => $request->quantity_used,
        ]);

        // 2. Record Waste (if any)
        if ($request->quantity_wasted && $request->quantity_wasted > 0) {
            MaterialWaste::create([
                'production_id' => $batch->production_id,
                'material_id' => $material->material_id,
                'quantity_wasted' => $request->quantity_wasted,
                'waste_reason' => $request->waste_reason ?? 'Production loss',
            ]);
        }

        // 3. Auto-Deduct Material Stock from Inventory
        $material->decrement('current_stock', $request->quantity_used);

        // 4. Update Production Batch Status/Yield
        $updateData = ['status' => $request->status];
        if ($request->actual_output) {
            $updateData['actual_output'] = $request->actual_output;
        }
        if ($request->status === 'completed') {
            $updateData['completion_date'] = date('Y-m-d');
        }

        $batch->update($updateData);

        return redirect()->route('production.index')->with('success', 'Material usage & waste recorded! Inventory stock updated.');
    }
}