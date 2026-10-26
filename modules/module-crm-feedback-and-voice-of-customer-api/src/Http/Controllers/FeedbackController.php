<?php

declare(strict_types=1);

namespace Liberu\CRM\FeedbackAndVoiceOfCustomerApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Liberu\CRM\FeedbackAndVoiceOfCustomer\Actions\CreateFeedbackSurvey;
use Liberu\CRM\FeedbackAndVoiceOfCustomer\Actions\DeliverFeedbackSurvey;
use Liberu\CRM\FeedbackAndVoiceOfCustomer\Actions\OpenFeedbackCase;
use Liberu\CRM\FeedbackAndVoiceOfCustomer\Actions\RecordFeedbackResponse;
use Liberu\CRM\FeedbackAndVoiceOfCustomer\Models\FeedbackDelivery;
use Liberu\CRM\FeedbackAndVoiceOfCustomer\Models\FeedbackResponse;
use Liberu\CRM\FeedbackAndVoiceOfCustomer\Models\FeedbackSurvey;
use Liberu\CRM\FeedbackAndVoiceOfCustomer\Queries\FeedbackQuery;

final class FeedbackController extends Controller
{
    public function __construct(private readonly FeedbackQuery $query) {}

    private function context(): array
    {
        $user = request()->user();

        return [(int) $user->current_team_id, (int) $user->id];
    }

    public function index(): JsonResponse
    {
        [$teamId] = $this->context();

        return response()->json($this->query->surveys($teamId)->get());
    }

    public function store(CreateFeedbackSurvey $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, request()->all()), 201);
    }

    public function deliver(FeedbackSurvey $survey, DeliverFeedbackSurvey $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, $survey, request('recipient_id'), (string) request('channel', 'email')), 201);
    }

    public function respond(FeedbackDelivery $delivery, RecordFeedbackResponse $action): JsonResponse
    {
        [$teamId] = $this->context();

        return response()->json($action->execute($teamId, $delivery, request()->all()), 201);
    }

    public function trend(FeedbackSurvey $survey): JsonResponse
    {
        [$teamId] = $this->context();

        return response()->json($this->query->trend($teamId, $survey->id));
    }

    public function openCase(FeedbackResponse $response, OpenFeedbackCase $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, $response, request('owner_id')), 201);
    }
}
