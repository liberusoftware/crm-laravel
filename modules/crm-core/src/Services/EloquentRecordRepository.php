<?php

declare(strict_types=1);

namespace Liberu\CRM\Core\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\CRM\Core\Contracts\RecordRepository;
use Liberu\CRM\Core\Models\Record;

final class EloquentRecordRepository implements RecordRepository
{
    public function find(string $type, int|string $id): ?Record
    {
        return Record::query()->where('record_type', $type)->find($id);
    }

    public function paginate(string $type, int $perPage = 25): LengthAwarePaginator
    {
        return Record::query()->where('record_type', $type)->paginate(min(max($perPage, 1), 100));
    }
}
