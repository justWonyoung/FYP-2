<?php

namespace App\Http\Controllers;

use App\Models\ProductionSchedulingSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductionSchedulingSettingController extends Controller
{
    public function index(Request $request): View
    {
        $setting = ProductionSchedulingSetting::query()
            ->where('is_active', true)
            ->orderByDesc('updated_at')
            ->first();

        return view('production.capacity', [
            'setting' => $setting,
            'canEdit' => $request->user()?->role === 'staff',
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'daily_capacity' => ['required', 'numeric', 'min:0.01'],
            'unit' => ['required', 'string', 'max:20'],
        ]);

        DB::transaction(function () use ($validated): void {
            $activeSettings = ProductionSchedulingSetting::query()
                ->where('is_active', true)
                ->lockForUpdate()
                ->get();

            $setting = $activeSettings->shift();

            if ($setting === null) {
                ProductionSchedulingSetting::query()->create([
                    'daily_capacity' => $validated['daily_capacity'],
                    'unit' => $validated['unit'],
                    'is_active' => true,
                ]);

                return;
            }

            $setting->update([
                'daily_capacity' => $validated['daily_capacity'],
                'unit' => $validated['unit'],
                'is_active' => true,
            ]);

            if ($activeSettings->isNotEmpty()) {
                ProductionSchedulingSetting::query()
                    ->whereKey($activeSettings->modelKeys())
                    ->update(['is_active' => false]);
            }
        });

        return redirect()
            ->route('production.capacity.index')
            ->with('success', 'Production capacity updated successfully.');
    }
}
