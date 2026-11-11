<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\AuditLogService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    public function index(Request $request): JsonResponse
    {
        if (! $request->user()->isAdmin()) {
            return $this->errorResponse('Forbidden: Administrator access required.', 403);
        }

        $logs = $this->auditLogService->getLogs(
            $request->only(['search', 'action', 'entity_type']),
            $request->integer('per_page', 20)
        );

        return $this->successResponse([
            'items' => $logs->items(),
            'pagination' => [
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'per_page' => $logs->perPage(),
                'total' => $logs->total(),
            ],
        ], 'Audit logs retrieved successfully');
    }
}
