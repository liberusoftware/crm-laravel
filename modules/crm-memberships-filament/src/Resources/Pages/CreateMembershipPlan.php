<?php

declare(strict_types=1);

namespace Liberu\CRM\MembershipsFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\MembershipsFilament\Resources\MembershipPlanResource;

final class CreateMembershipPlan extends CreateRecord
{
    protected static string $resource = MembershipPlanResource::class;
}
