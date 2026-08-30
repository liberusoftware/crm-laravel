<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataModel\Contracts;

use Liberu\CRM\CustomerDataModel\Models\ObjectDefinition;

interface SchemaValidator
{
    /** @param array<string, mixed> $values @return array<string, list<string>> */
    public function validate(ObjectDefinition $object, array $values, ?string $stage = null): array;
}
