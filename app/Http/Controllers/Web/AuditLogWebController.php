<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogWebController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    public function index(Request $request): View
    {
        if (! $request->user()->isAdmin()) {
            abort(403, 'Hanya Administrator yang diizinkan melihat Log Audit Sistem.');
        }

        $logs = $this->auditLogService->getLogs(
            $request->only(['search', 'action', 'entity_type']),
            20
        );

        return view('audit_logs.index', [
            'logs' => $logs,
            'filters' => $request->only(['search', 'action', 'entity_type']),
        ]);
    }
}
