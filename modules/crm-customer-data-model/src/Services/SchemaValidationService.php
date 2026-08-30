<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataModel\Services;

use Liberu\CRM\CustomerDataModel\Contracts\SchemaValidator;
use Liberu\CRM\CustomerDataModel\Models\FieldDefinition;
use Liberu\CRM\CustomerDataModel\Models\ObjectDefinition;

final class SchemaValidationService implements SchemaValidator
{
    public function validate(ObjectDefinition $object, array $values, ?string $stage = null): array
    {
        $errors = [];
        foreach ($object->fields()->orderBy('position')->get() as $field) {
            if ($field->is_calculated) {
                continue;
            }
            $required = $field->is_required || ($stage !== null && in_array($stage, $field->required_stages ?? [], true));
            $value = $values[$field->key] ?? null;
            if ($required && ($value === null || $value === '')) {
                $errors[$field->key][] = 'This field is required.';

                continue;
            }
            if ($value !== null && ! $this->matchesType($field, $value)) {
                $errors[$field->key][] = 'The value does not match the field type.';
            }
            if ($value !== null && isset($field->config['options']) && is_array($field->config['options']) && ! in_array($value, $field->config['options'], true)) {
                $errors[$field->key][] = 'The selected value is not allowed.';
            }
        }

        return $errors;
    }

    private function matchesType(FieldDefinition $field, mixed $value): bool
    {
        return match ($field->type) {
            'number' => is_int($value) || is_float($value) || (is_string($value) && is_numeric($value)),
            'boolean' => is_bool($value),
            'date', 'datetime' => is_string($value),
            'json', 'multi_select' => is_array($value),
            default => is_string($value) || is_numeric($value),
        };
    }
}
