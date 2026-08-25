<?php

declare(strict_types=1);

namespace Liberu\CRM\Segmentation\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\Segmentation\Events\AudienceRefreshed;
use Liberu\CRM\Segmentation\Models\Audience;
use Liberu\CRM\Segmentation\Models\AudienceMember;
use Liberu\CRM\Segmentation\Services\SegmentationAudit;
use Liberu\CRM\Segmentation\Services\SegmentationPolicy;

final class RefreshAudience
{
    public function execute(int $teamId, int $actorId, int $audienceId, array $data = []): Audience
    {
        if (! app(SegmentationPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }$audience = Audience::query()->where('team_id', $teamId)->findOrFail($audienceId);
        $ids = $data['contact_ids'] ?? [];
        if (! is_array($ids)) {
            throw ValidationException::withMessages(['contact_ids' => 'Contact IDs must be an array.']);
        }$excluded = array_map('intval', $audience->exclusions ?? []);
        $ids = array_values(array_unique(array_diff(array_map('intval', $ids), $excluded)));
        DB::transaction(function () use ($teamId, $actorId, $audience, $ids) {
            AudienceMember::query()->where('team_id', $teamId)->where('audience_id', $audience->id)->delete();
            foreach ($ids as $contactId) {
                AudienceMember::query()->create(['team_id' => $teamId, 'audience_id' => $audience->id, 'contact_id' => $contactId, 'attributes' => $audience->calculated_attributes ?? [], 'included_at' => now()]);
            }$audience->forceFill(['status' => 'active', 'estimated_count' => count($ids), 'refreshed_at' => now()])->save();
            app(SegmentationAudit::class)->record($teamId, $actorId, $audience->id, 'audience_refreshed', ['count' => count($ids)]);
        });
        AudienceRefreshed::dispatch($audience, count($ids));

        return $audience->fresh();
    }
}
