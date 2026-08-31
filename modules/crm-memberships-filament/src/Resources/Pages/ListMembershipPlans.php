<?php

declare(strict_types=1);

namespace Liberu\CRM\MembershipsFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\MembershipsFilament\Resources\MembershipPlanResource;

final class ListMembershipPlans extends ListRecords
{
    protected static string $resource = MembershipPlanResource::class;
}
