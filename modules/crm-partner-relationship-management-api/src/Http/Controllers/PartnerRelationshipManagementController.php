<?php

declare(strict_types=1);

namespace Liberu\CRM\PartnerRelationshipManagementApi\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Liberu\CRM\PartnerRelationshipManagement\Actions\AddPartnerContact;
use Liberu\CRM\PartnerRelationshipManagement\Actions\ChangePartnerStatus;
use Liberu\CRM\PartnerRelationshipManagement\Actions\CreatePartner;
use Liberu\CRM\PartnerRelationshipManagement\Actions\RecordPartnerActivity;
use Liberu\CRM\PartnerRelationshipManagement\Actions\RecordPartnerPerformance;
use Liberu\CRM\PartnerRelationshipManagement\Queries\PartnerQuery;

final class PartnerRelationshipManagementController extends Controller
{
    private function team(Request $r): int
    {
        abort_unless($r->user()?->current_team_id !== null, 403);

        return (int) $r->user()->current_team_id;
    }

    public function partners(Request $r, PartnerQuery $q)
    {
        return response()->json(['data' => $q->partners($this->team($r))->get()]);
    }

    public function partner(Request $r, CreatePartner $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function contact(Request $r, AddPartnerContact $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function status(Request $r, int $partner, string $status, ChangePartnerStatus $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $partner, $status)]);
    }

    public function activity(Request $r, RecordPartnerActivity $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function performance(Request $r, RecordPartnerPerformance $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }
}
