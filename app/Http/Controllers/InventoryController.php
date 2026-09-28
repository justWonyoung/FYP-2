<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $materials = Material::orderBy('material_name', 'asc')->get();
        return view('inventory.index', compact('materials'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'material_name' => 'required|string|max:255',
            'material_type' => 'required|string',
            'unit'          => 'required|string',
            'current_stock' => 'required|numeric|min:0',
            'minimum_stock' => 'required|numeric|min:0',
        ]);

        Material::create($validated);

        return redirect()->back()->with('success', 'Material added to inventory successfully.');
    }
}