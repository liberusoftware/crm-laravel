<?php

declare(strict_types=1);

namespace App\Services\Marketing;

use App\Models\SocialMediaPost;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final class PostWorkflow
{
    public function __construct(private readonly WorkspaceAccess $access, private readonly PlatformConfiguration $configuration, private readonly PostTargets $targets) {}

    public function prepare(User $user, Team $team, array $input, string $permission = 'create_social_media_post'): array
    {
        $this->access->authorize($user, $team, $permission);
        Validator::make($input, ['platforms' => ['required', 'array', 'min:1', 'max:50']])->validate();
        $input['platforms'] = $this->targets->normalize($team, $input['platforms'], $this->configuration->provider());

        $data = Validator::make($input, [
            'content' => ['required', 'string', 'max:65535'],
            'platforms' => ['required', 'array', 'min:1'], 'platforms.*' => ['required'],
            'status' => ['required', 'in:draft,scheduled'],
            'scheduled_at' => ['required_if:status,scheduled', 'nullable', 'date', 'after:now'],
            'link' => ['nullable', 'url:https', 'max:2048'], 'video_url' => ['nullable', 'url:https', 'max:2048'],
            'image_path' => ['nullable', 'string', 'max:1000'],
        ])->validate();
        if (filled($data['scheduled_at'] ?? null)) {
            $data['scheduled_at'] = Carbon::parse($data['scheduled_at'], config('app.timezone', 'UTC'))->setTimezone(config('app.timezone', 'UTC'));
        }

        return $data + ['team_id' => $team->getKey()];
    }

    public function update(User $user, Team $team, SocialMediaPost $post, array $input): SocialMediaPost
    {
        abort_unless((int) $post->getAttribute('team_id') === (int) $team->getKey(), 404);
        $data = $this->prepare($user, $team, array_replace($input, ['status' => 'draft']), 'update_social_media_post');

        return DB::transaction(function () use ($team, $post, $data): SocialMediaPost {
            $locked = SocialMediaPost::query()->where('team_id', $team->getKey())->whereKey($post->getKey())->lockForUpdate()->firstOrFail();
            abort_unless(in_array($locked->status, ['draft', 'scheduled'], true), 422, 'Publication has started. Refresh the post before editing.');
            $locked->update($data);

            return $locked;
        });
    }

    public function approve(User $user, Team $team, SocialMediaPost $post): void
    {
        abort_unless((int) $post->getAttribute('team_id') === (int) $team->getKey(), 404);
        abort_unless($this->access->canManage($user, $team), 403);
        abort_unless(in_array($post->status, ['draft', 'scheduled'], true), 422);
        $post->update(['approved_at' => now(), 'approved_by' => $user->getKey()]);
    }

    public function schedule(User $user, Team $team, SocialMediaPost $post, string $date): void
    {
        $this->access->authorize($user, $team, 'update_social_media_post');
        abort_unless((int) $post->getAttribute('team_id') === (int) $team->getKey(), 404);
        abort_unless(in_array($post->status, ['draft', 'scheduled'], true), 422);
        $data = Validator::make(['scheduled_at' => $date], ['scheduled_at' => ['required', 'date', 'after:now']])->validate();
        $provider = $this->configuration->provider();
        $targets = $this->targets->normalize($team, $post->platforms, $provider);
        if ($provider === 'zernio' && collect($targets)->contains(fn ($target) => ! is_array($target))) {
            throw ValidationException::withMessages(['platforms' => 'Select connected accounts in the post editor before scheduling with Zernio.']);
        }
        $preferences = $team->getAttribute('marketing_preferences') ?? [];
        abort_if(($preferences['approval_required'] ?? true) && ! $post->getAttribute('approved_at'), 422, 'Approve the content before scheduling.');
        $post->update(['scheduled_at' => Carbon::parse($data['scheduled_at'], config('app.timezone', 'UTC'))->setTimezone(config('app.timezone', 'UTC')), 'status' => 'scheduled', 'publishing_provider' => $this->configuration->provider()]);
    }

    public function reconcile(User $user, Team $team, SocialMediaPost $post, array $input): void
    {
        abort_unless((int) $post->getAttribute('team_id') === (int) $team->getKey(), 404);
        abort_unless($this->access->canManage($user, $team), 403);
        $data = Validator::make($input, ['resolution' => ['required', 'in:published,failed'], 'evidence' => ['required', 'string', 'min:10', 'max:1500']])->validate();
        $updated = SocialMediaPost::query()->whereKey($post->getKey())->where('status', 'publishing_unknown')->update([
            'status' => $data['resolution'], 'last_error' => 'Reconciled by user '.$user->getKey().': '.$data['evidence'],
        ]);
        abort_unless($updated === 1, 409, 'The delivery state has changed. Refresh before reconciling.');
    }
}
