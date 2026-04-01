<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Cct;
use App\Models\Delivery;
use App\Models\DeliveryValidation;
use App\Models\Direction;
use App\Models\DirectionStock;
use App\Models\Plant;
use App\Models\Role;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $summary = [
            'users' => User::count(),
            'active_users' => User::active()->count(),
            'roles' => Role::count(),
            'ccts' => Cct::count(),
            'plants' => Plant::count(),
            'directions' => Direction::count(),
            'deliveries_pending' => Delivery::where('status', 'pending')->count(),
            'validations_pending' => DeliveryValidation::where('status', 'pending')->count(),
            'stock_records' => DirectionStock::count(),
            'stock_total' => DirectionStock::sum('stock'),
        ];

        $recentLogs = ActivityLog::with(['user.role'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('summary', 'recentLogs'));
    }
}
