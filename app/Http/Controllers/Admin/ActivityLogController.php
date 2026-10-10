<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();
        $module = $request->string('module')->trim()->toString();
        $auditTableAvailable = Schema::hasTable('activity_logs');

        if (! $auditTableAvailable) {
            return view('admin.activity-logs.index', [
                'activityLogs' => new LengthAwarePaginator([], 0, 20),
                'modules' => collect(),
                'search' => $search,
                'module' => $module,
                'auditTableAvailable' => false,
            ]);
        }

        $activityLogs = ActivityLog::query()
            ->with('user:user_id,full_name,username')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('action', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($query) => $query->where('full_name', 'like', "%{$search}%")
                            ->orWhere('username', 'like', "%{$search}%"));
                });
            })
            ->when($module !== '', fn ($query) => $query->where('module', $module))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $modules = ActivityLog::query()->distinct()->orderBy('module')->pluck('module');

        return view('admin.activity-logs.index', compact('activityLogs', 'modules', 'search', 'module', 'auditTableAvailable'));
    }
}
