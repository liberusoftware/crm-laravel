<?php

declare(strict_types=1);

namespace Liberu\CRM\SaasPackaging\Filament\Resources\SubscriptionResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\SaasPackaging\Filament\Resources\SubscriptionResource;

final class ListSubscriptions extends ListRecords
{
    protected static string $resource = SubscriptionResource::class;
}
