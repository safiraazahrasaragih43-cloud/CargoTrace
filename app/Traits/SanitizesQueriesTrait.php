<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait SanitizesQueriesTrait
{
    /**
     * Escape SQL LIKE wildcard characters on drivers supporting backslash escaping (MySQL/MariaDB/PgSQL).
     */
    protected function escapeLike(string $value): string
    {
        $trimmed = trim($value);
        $driver = DB::connection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb', 'pgsql'], true)) {
            return str_replace(
                ['\\', '%', '_'],
                ['\\\\', '\\%', '\\_'],
                $trimmed
            );
        }

        return $trimmed;
    }

    /**
     * Constrain pagination page size to prevent excessive result-set requests.
     */
    protected function clampPerPage(int $perPage, int $max = 100): int
    {
        return max(1, min($max, $perPage));
    }
}
