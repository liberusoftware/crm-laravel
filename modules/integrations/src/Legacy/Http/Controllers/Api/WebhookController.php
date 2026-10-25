<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\SsoException;
use App\Http\Controllers\Controller;
use App\Models\Webhook;
use App\Services\WebhookService;
use App\Support\SsrfGuard;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class WebhookController extends Controller
{
    public function __construct(private readonly WebhookService $webhooks) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json($this->owned($request)->latest('id')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);
        $data['team_id'] = $request->user()?->getAttribute('current_team_id');
        $data['user_id'] = $request->user()?->getAuthIdentifier();

        return response()->json($this->webhooks->register($data)->makeVisible('secret'), 201);
    }

    public function show(Request $request, Webhook $webhook): JsonResponse
    {
        return response()->json($this->owned($request)->findOrFail($webhook->getKey())->makeVisible('secret'));
    }

    public function update(Request $request, Webhook $webhook): JsonResponse
    {
        $data = $this->validated($request, true);
        $model = $this->owned($request)->findOrFail($webhook->getKey());

        return response()->json($this->webhooks->update($model, $data));
    }

    public function destroy(Request $request, Webhook $webhook): JsonResponse
    {
        $this->owned($request)->findOrFail($webhook->getKey())->delete();

        return response()->json(null, 204);
    }

    public function regenerateSecret(Request $request, Webhook $webhook): JsonResponse
    {
        return response()->json(['secret' => $this->webhooks->regenerateSecret($this->owned($request)->findOrFail($webhook->getKey()))]);
    }

    public function events(): JsonResponse
    {
        return response()->json(['events' => Webhook::EVENTS]);
    }

    private function owned(Request $request): Builder
    {
        $teamId = $request->user()?->getAttribute('current_team_id');

        return $teamId === null
            ? Webhook::query()->whereNull('team_id')->where('user_id', $request->user()?->getAuthIdentifier())
            : Webhook::query()->where('team_id', $teamId);
    }

    private function validated(Request $request, bool $updating = false): array
    {
        $presence = $updating ? 'sometimes' : 'required';
        $data = $request->validate([
            'name' => [$presence, 'required', 'string', 'max:255'],
            'url' => [$presence, 'required', 'url:http,https', 'max:2048'],
            'events' => [$presence, 'required', 'array', 'min:1'],
            'events.*' => ['string', Rule::in(Webhook::EVENTS)],
            'is_active' => ['sometimes', 'boolean'],
        ]);
        if (app()->isProduction() && isset($data['url'])) {
            try {
                SsrfGuard::assertPublicHttps($data['url']);
            } catch (SsoException) {
                throw ValidationException::withMessages(['url' => 'A public HTTPS webhook URL is required.']);
            }
        }

        return $data;
    }
}
