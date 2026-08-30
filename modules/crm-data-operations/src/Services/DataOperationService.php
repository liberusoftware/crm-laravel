<?php

declare(strict_types=1);

namespace Liberu\CRM\DataOperations\Services;

use Illuminate\Support\Str;

final class DataOperationService
{
    /** @param array<string, mixed> $row @param array<string, array<string, mixed>> $mapping */
    public function mapAndNormalize(array $row, array $mapping): array
    {
        $result = [];
        foreach ($mapping as $source => $definition) {
            if (! array_key_exists($source, $row)) {
                continue;
            }
            $value = $row[$source];
            $transform = $definition['transform'] ?? null;
            $value = match ($transform) {
                'trim' => is_string($value) ? trim($value) : $value, 'lowercase' => is_string($value) ? Str::lower(trim($value)) : $value, 'uppercase' => is_string($value) ? Str::upper(trim($value)) : $value, default => $value
            };
            $result[(string) ($definition['target_field'] ?? $source)] = $value;
        }

        return $result;
    }

    /** @param array<string, mixed> $left @param array<string, mixed> $right @param array<int, string> $fields */
    public function duplicateConfidence(array $left, array $right, array $fields): float
    {
        if ($fields === []) {
            return 0.0;
        }
        $matches = 0;
        foreach ($fields as $field) {
            if (isset($left[$field], $right[$field]) && Str::lower(trim((string) $left[$field])) === Str::lower(trim((string) $right[$field]))) {
                $matches++;
            }
        }

        return round($matches / count($fields), 4);
    }
}
