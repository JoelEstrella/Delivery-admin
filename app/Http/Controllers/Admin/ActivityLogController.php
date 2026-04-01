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
        $records = ActivityLog::with(['user.role'])
            ->when($request->get('search'), function ($query, $search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('module', 'like', '%' . $search . '%')
                        ->orWhere('action', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
                });
            })
            ->latest()
            ->paginate(15)
            ->appends($request->query());

        return view('admin.shared.index', [
            'title' => 'Bitácora',
            'subtitle' => 'Registro de acciones importantes',
            'search' => $request->get('search'),
            'records' => $records,
            'columns' => [
                ['label' => 'Fecha', 'field' => 'created_at', 'type' => 'datetime'],
                ['label' => 'Módulo', 'field' => 'module', 'type' => 'text'],
                ['label' => 'Acción', 'field' => 'action', 'type' => 'text'],
                ['label' => 'Descripción', 'field' => 'description', 'type' => 'text'],
                ['label' => 'Usuario', 'field' => 'user.name', 'type' => 'text'],
            ],
            'resource' => null,
            'actions' => [],
        ]);
    }
}
