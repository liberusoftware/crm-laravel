<?php

declare(strict_types=1);

namespace Liberu\CRM\Core\Enums;

enum RecordType: string
{
    case Contact = 'contact';
    case Organization = 'organization';
    case Household = 'household';
}
