<?php

declare(strict_types=1);

namespace Liberu\CRM\Knowledge\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\Knowledge\Models\KnowledgeArticle;
use Liberu\CRM\Knowledge\Models\KnowledgeEvent;
use Liberu\CRM\Knowledge\Services\KnowledgePolicy;

final class RecordKnowledgeEvent
{
    public function __construct(private readonly KnowledgePolicy $policy) {}

    public function execute(int $teamId, int $userId, KnowledgeArticle $article, array $input): KnowledgeEvent
    {
        abort_unless($article->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['kind' => ['required', 'in:review,approval,feedback,case_link,suggested_answer,stale_marked,localization'], 'status' => ['required', 'in:pending,approved,rejected,recorded'], 'details' => ['nullable', 'string'], 'payload' => ['nullable', 'array']])->validate();
        $event = KnowledgeEvent::query()->create(['team_id' => $teamId, 'article_id' => $article->id, 'actor_id' => $userId, ...$data]);
        if ($event->kind === 'approval' && $event->status === 'approved') {
            $article->update(['status' => 'published', 'reviewed_at' => now()]);
        }if ($event->kind === 'stale_marked') {
            $article->update(['stale_at' => now()]);
        }

        return $event;
    }
}
