<?php

declare(strict_types=1);

namespace Liberu\CRM\MembershipsFilament\Resources\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\MembershipsFilament\Resources\MembershipPlanResource;

final class EditMembershipPlan extends EditRecord
{
    protected static string $resource = MembershipPlanResource::class;
}
