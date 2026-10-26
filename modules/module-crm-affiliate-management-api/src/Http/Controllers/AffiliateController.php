<?php

declare(strict_types=1);

namespace Liberu\CRM\AffiliateManagementApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Liberu\CRM\AffiliateManagement\Actions\ApplyAffiliate;
use Liberu\CRM\AffiliateManagement\Actions\ApproveAffiliate;
use Liberu\CRM\AffiliateManagement\Actions\CreateAffiliateLink;
use Liberu\CRM\AffiliateManagement\Actions\RecordAffiliateEvent;
use Liberu\CRM\AffiliateManagement\Models\Affiliate;
use Liberu\CRM\AffiliateManagement\Models\AffiliateLink;
use Liberu\CRM\AffiliateManagement\Queries\AffiliateQuery;

final class AffiliateController extends Controller
{
    public function __construct(private readonly AffiliateQuery $query) {}

    private function context(): array
    {
        $user = request()->user();

        return [(int) $user->current_team_id, (int) $user->id];
    }

    public function index(): JsonResponse
    {
        return response()->json($this->query->affiliates($this->context()[0])->get());
    }

    public function store(ApplyAffiliate $action): JsonResponse
    {
        return response()->json($action->execute($this->context()[0], request()->all()), 201);
    }

    public function approve(Affiliate $affiliate, ApproveAffiliate $action): JsonResponse
    {
        return response()->json($action->execute($this->context()[0], $affiliate));
    }

    public function link(Affiliate $affiliate, CreateAffiliateLink $action): JsonResponse
    {
        return response()->json($action->execute($this->context()[0], $affiliate, request()->all()), 201);
    }

    public function event(AffiliateLink $link, RecordAffiliateEvent $action): JsonResponse
    {
        return response()->json($action->execute($this->context()[0], $link, request()->all()), 201);
    }

    public function events(): JsonResponse
    {
        return response()->json($this->query->events($this->context()[0])->get());
    }
}
