<?php

declare(strict_types=1);

namespace Liberu\CRM\Communities\Actions;

use Liberu\CRM\Communities\Models\CommunityContent;
use Liberu\CRM\Communities\Models\CommunityMembership;
use Liberu\CRM\Communities\Models\CommunitySpace;

final class PublishCommunityContent
{
    public function execute(int $teamId, CommunitySpace $space, string $authorKey, string $body, string $kind = 'post', ?int $parentId = null, array $metadata = []): CommunityContent
    {
        abort_unless((int) $space->team_id === $teamId && $space->status === 'active' && $authorKey !== '' && trim($body) !== '' && in_array($kind, ['post', 'comment', 'event', 'knowledge', 'activity'], true), 422);
        $member = CommunityMembership::query()->where('space_id', $space->id)->where('subject_key', $authorKey)->where('status', 'active')->first();
        abort_unless($member !== null, 403);
        $member->increment('points', $kind === 'post' ? 10 : 2);

        return CommunityContent::query()->create(['team_id' => $teamId, 'space_id' => $space->id, 'author_key' => $authorKey, 'kind' => $kind, 'status' => 'published', 'body' => $body, 'parent_id' => $parentId, 'metadata' => $metadata]);
    }
}
