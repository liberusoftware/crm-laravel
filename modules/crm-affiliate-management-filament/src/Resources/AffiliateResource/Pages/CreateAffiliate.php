<?php

declare(strict_types=1);

namespace Liberu\CRM\AffiliateManagementFilament\Resources\AffiliateResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\AffiliateManagement\Actions\ApplyAffiliate;
use Liberu\CRM\AffiliateManagementFilament\Resources\AffiliateResource;

final class CreateAffiliate extends CreateRecord
{
    protected static string $resource = AffiliateResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $teamId = auth()->user()?->getAttribute('current_team_id');
        abort_unless(is_numeric($teamId) && (int) $teamId > 0, 403);

        return app(ApplyAffiliate::class)->execute((int) $teamId, $data);
    }
}
