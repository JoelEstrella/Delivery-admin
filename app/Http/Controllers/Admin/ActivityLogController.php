<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:logs.view');
    }

   public function index(Request $request)
    {
        $search = trim((string) $request->get('search'));

        $query = ActivityLog::with(['user:id,name,role_id', 'user.role:id,name'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('module', 'like', '%' . $search . '%')
                        ->orWhere('action', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%')
                        ->orWhere('ip_address', 'like', '%' . $search . '%')
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->latest();

        if ($request->expectsJson() || $request->ajax()) {
            $records = $query->paginate(15)->through(function ($log) {
                return [
                    'id' => $log->id,
                    'created_at' => optional($log->created_at)->format('d/m/Y H:i'),
                    'module' => $log->module,
                    'action' => $log->action,
                    'description' => $log->description,
                    'record_id' => $log->record_id,
                    'ip_address' => $log->ip_address,
                    'user_agent' => $log->user_agent,
                    'old_values' => $log->old_values,
                    'new_values' => $log->new_values,
                    'user' => [
                        'name' => optional($log->user)->name,
                        'role' => optional(optional($log->user)->role)->name,
                    ],
                ];
            });

            return response()->json($records);
        }

        return view('admin.activity-logs.index', [
            'title' => 'Bitácora',
            'subtitle' => 'Registro de acciones importantes',
        ]);
    }
}
