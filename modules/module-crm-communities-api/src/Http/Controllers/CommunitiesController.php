<?php

declare(strict_types=1);

namespace Liberu\CRM\CommunitiesApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Liberu\CRM\Communities\Actions\CreateCommunitySpace;
use Liberu\CRM\Communities\Actions\JoinCommunity;
use Liberu\CRM\Communities\Actions\PublishCommunityContent;
use Liberu\CRM\Communities\Models\CommunitySpace;
use Liberu\CRM\Communities\Queries\CommunityQuery;

final class CommunitiesController extends Controller
{
    public function __construct(private readonly CommunityQuery $query) {}

    private function context(): array
    {
        $u = request()->user();

        return [(int) $u->current_team_id, (int) $u->id];
    }

    public function index(): JsonResponse
    {
        [$t] = $this->context();

        return response()->json($this->query->spaces($t)->get());
    }

    public function store(CreateCommunitySpace $a): JsonResponse
    {
        [$t,$u] = $this->context();

        return response()->json($a->execute($t, $u, (string) request('name'), (string) request('kind', 'customer'), (array) request('settings', [])), 201);
    }

    public function join(CommunitySpace $space, JoinCommunity $a): JsonResponse
    {
        [$t,$u] = $this->context();

        return response()->json($a->execute($t, $space, (string) request('subject_key', (string) $u), (string) request('role', 'member')), 201);
    }

    public function content(CommunitySpace $space, PublishCommunityContent $a): JsonResponse
    {
        [$t,$u] = $this->context();

        return response()->json($a->execute($t, $space, (string) request('author_key', (string) $u), (string) request('body'), (string) request('kind', 'post'), request('parent_id'), (array) request('metadata', [])), 201);
    }

    public function feed(CommunitySpace $space): JsonResponse
    {
        [$t] = $this->context();

        return response()->json($this->query->feed($t, $space->id)->get());
    }
}
