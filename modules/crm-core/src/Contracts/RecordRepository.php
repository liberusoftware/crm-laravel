<?php

declare(strict_types=1);

namespace Liberu\CRM\Core\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\CRM\Core\Models\Record;

interface RecordRepository
{
    public function find(string $type, int|string $id): ?Record;

    public function paginate(string $type, int $perPage = 25): LengthAwarePaginator;
}
