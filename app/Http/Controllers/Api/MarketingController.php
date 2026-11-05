<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Marketing\CampaignOperationRequest;
use App\Http\Requests\Marketing\SchedulePostRequest;
use App\Models\Campaign;
use App\Models\SocialMediaPost;
use App\Models\Team;
use App\Models\User;
use App\Services\Marketing\AdvertisingManager;
use App\Services\Marketing\ContentAssistant;
use App\Services\Marketing\PostWorkflow;
use App\Services\Marketing\WorkspaceAccess;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class MarketingController extends Controller
{
    private function context(Request $request, string $permission): array
    {
        $user = $request->user();
        abort_unless($user instanceof User, 401);
        $team = $user->currentTeam;
        abort_unless($team instanceof Team, 403);
        app(WorkspaceAccess::class)->authorize($user, $team, $permission);

        return [$user, $team];
    }

    public function index(Request $request): JsonResponse
    {
        [, $team] = $this->context($request, 'view_social_media_post');

        return response()->json(SocialMediaPost::query()->where('team_id', $team->getKey())->orderBy('scheduled_at')->paginate(50));
    }

    public function store(Request $request, PostWorkflow $workflow): JsonResponse
    {
        [$user, $team] = $this->context($request, 'create_social_media_post');
        $data = $workflow->prepare($user, $team, array_replace($request->all(), ['status' => 'draft']));

        return response()->json(SocialMediaPost::query()->create($data), 201);
    }

    public function update(Request $request, SocialMediaPost $post, PostWorkflow $workflow): JsonResponse
    {
        [$user, $team] = $this->context($request, 'update_social_media_post');

        return response()->json($workflow->update($user, $team, $post, $request->all()));
    }

    public function approve(Request $request, SocialMediaPost $post, PostWorkflow $workflow): JsonResponse
    {
        [$user, $team] = $this->context($request, 'update_social_media_post');
        $workflow->approve($user, $team, $post);

        return response()->json($post->refresh());
    }

    public function schedule(SchedulePostRequest $request, SocialMediaPost $post, PostWorkflow $workflow): JsonResponse
    {
        [$user, $team] = $this->context($request, 'update_social_media_post');
        $data = $request->validated();
        $workflow->schedule($user, $team, $post, $data['scheduled_at']);

        return response()->json($post->refresh());
    }

    public function cancel(Request $request, SocialMediaPost $post): JsonResponse
    {
        [, $team] = $this->context($request, 'update_social_media_post');
        abort_unless((int) $post->getAttribute('team_id') === (int) $team->getKey(), 404);
        $updated = SocialMediaPost::query()->whereKey($post->getKey())->whereIn('status', ['draft', 'scheduled'])->update(['status' => 'cancelled']);
        abort_unless($updated === 1, 409, 'The post has already been submitted. Reconcile it with the provider.');

        return response()->json($post->refresh());
    }

    public function generate(Request $request, ContentAssistant $assistant): JsonResponse
    {
        [$user, $team] = $this->context($request, 'create_social_media_post');

        return response()->json($assistant->generate($user, $team, $request->all()));
    }

    public function campaign(CampaignOperationRequest $request, Campaign $campaign, AdvertisingManager $manager): JsonResponse
    {
        [$user, $team] = $this->context($request, 'update_campaign');
        $data = $request->validated();

        return response()->json($manager->execute($user, $team, $campaign, $data['operation']));
    }
}
