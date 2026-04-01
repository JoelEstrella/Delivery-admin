<?php

namespace App\Services;

use App\Repositories\ActivityLogRepository;
use Illuminate\Support\Facades\Auth;

class ActivityLogService
{
    protected $logs;

    public function __construct(ActivityLogRepository $logs)
    {
        $this->logs = $logs;
    }

    public function log($module, $action, $description, $recordId = null, array $oldValues = null, array $newValues = null, $userId = null)
    {
        if ($userId === null) {
            $userId = Auth::id();
        }

        $ipAddress = null;
        $userAgent = null;

        if (!app()->runningInConsole()) {
            $request = request();
            $ipAddress = $request->ip();
            $userAgent = $request->userAgent();
        }

        return $this->logs->create([
            'user_id' => $userId,
            'module' => $module,
            'action' => $action,
            'description' => $description,
            'record_id' => $recordId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }
}
