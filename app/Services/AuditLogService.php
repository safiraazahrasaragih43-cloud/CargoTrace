<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AuditLogService
{
    /**
     * Record an immutable audit trail entry for critical operational actions.
     */
    public function record(
        string $action,
        string $entityType,
        ?int $entityId,
        string $description,
        ?User $actor = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): AuditLog {
        $resolvedActorId = $actor?->id ?? (auth()->check() ? auth()->id() : null);

        return AuditLog::create([
            'user_id' => $resolvedActorId,
            'action' => strtoupper($action),
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()?->ip(),
        ]);
    }

    /**
     * Query audit logs with search, action, and entity filters (Admin view).
     */
    public function getLogs(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = AuditLog::with('actor');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('entity_type', 'like', "%{$search}%")
                  ->orWhereHas('actor', fn ($aq) => $aq->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            });
        }

        if (! empty($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        if (! empty($filters['entity_type'])) {
            $query->where('entity_type', $filters['entity_type']);
        }

        return $query->latest()->paginate($perPage);
    }
}
