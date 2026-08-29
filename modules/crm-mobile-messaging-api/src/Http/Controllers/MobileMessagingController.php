<?php

declare(strict_types=1);

namespace Liberu\CRM\MobileMessagingApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\CRM\MobileMessaging\Actions\CreateCampaign;
use Liberu\CRM\MobileMessaging\Actions\RecordMessage;
use Liberu\CRM\MobileMessaging\Actions\UpsertContactConsent;
use Liberu\CRM\MobileMessaging\Models\MessagingCampaign;
use Liberu\CRM\MobileMessaging\Queries\CampaignQuery;

final class MobileMessagingController
{
    private function c(Request $r): array
    {
        return [(int) $r->user()->current_team_id, (int) $r->user()->id];
    }

    public function index(Request $r, CampaignQuery $q): JsonResponse
    {
        return response()->json($q->forTeam($this->c($r)[0])->paginate());
    }

    public function consent(Request $r, UpsertContactConsent $a): JsonResponse
    {
        [$t,$u] = $this->c($r);

        return response()->json($a->execute($t, $u, $r->all()), 201);
    }

    public function store(Request $r, CreateCampaign $a): JsonResponse
    {
        [$t,$u] = $this->c($r);

        return response()->json($a->execute($t, $u, $r->all()), 201);
    }

    public function message(Request $r, MessagingCampaign $campaign, RecordMessage $a): JsonResponse
    {
        [$t,$u] = $this->c($r);

        return response()->json($a->execute($t, $u, $campaign, $r->all()), 201);
    }
}
