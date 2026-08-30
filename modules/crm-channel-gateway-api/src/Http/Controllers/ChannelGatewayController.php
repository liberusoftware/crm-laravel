<?php

declare(strict_types=1);

namespace Liberu\CRM\ChannelGatewayApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Liberu\CRM\ChannelGateway\Actions\QueueGatewayDelivery;
use Liberu\CRM\ChannelGateway\Actions\RegisterGatewayChannel;
use Liberu\CRM\ChannelGateway\Actions\UpdateGatewayHealth;
use Liberu\CRM\ChannelGateway\Models\GatewayChannel;
use Liberu\CRM\ChannelGateway\Queries\GatewayQuery;

final class ChannelGatewayController extends Controller
{
    public function __construct(private readonly GatewayQuery $query) {}

    private function team(): int
    {
        return (int) request()->user()->current_team_id;
    }

    public function index(): JsonResponse
    {
        return response()->json($this->query->channels($this->team())->get());
    }

    public function store(RegisterGatewayChannel $a): JsonResponse
    {
        return response()->json($a->execute($this->team(), (string) request('key'), (string) request('kind'), (string) request('provider'), (array) request('configuration', [])), 201);
    }

    public function delivery(GatewayChannel $channel, QueueGatewayDelivery $a): JsonResponse
    {
        return response()->json($a->execute($this->team(), $channel, (string) request('idempotency_key'), (string) request('address'), (string) request('body'), (array) request('metadata', [])), 201);
    }

    public function health(GatewayChannel $channel, UpdateGatewayHealth $a): JsonResponse
    {
        return response()->json($a->execute($this->team(), $channel, (bool) request('healthy'), request('failure')));
    }
}
