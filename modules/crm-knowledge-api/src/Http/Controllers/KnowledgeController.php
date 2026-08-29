<?php

declare(strict_types=1);

namespace Liberu\CRM\KnowledgeApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\CRM\Knowledge\Actions\CreateArticle;
use Liberu\CRM\Knowledge\Actions\RecordKnowledgeEvent;
use Liberu\CRM\Knowledge\Models\KnowledgeArticle;
use Liberu\CRM\Knowledge\Queries\KnowledgeQuery;

final class KnowledgeController
{
    private function c(Request $r): array
    {
        return [(int) $r->user()->current_team_id, (int) $r->user()->id];
    }

    public function index(Request $r, KnowledgeQuery $q): JsonResponse
    {
        return response()->json($q->forTeam($this->c($r)[0], $r->string('search')->toString())->paginate());
    }

    public function store(Request $r, CreateArticle $a): JsonResponse
    {
        [$t,$u] = $this->c($r);

        return response()->json($a->execute($t, $u, $r->all()), 201);
    }

    public function event(Request $r, KnowledgeArticle $article, RecordKnowledgeEvent $a): JsonResponse
    {
        [$t,$u] = $this->c($r);

        return response()->json($a->execute($t, $u, $article, $r->all()), 201);
    }
}
