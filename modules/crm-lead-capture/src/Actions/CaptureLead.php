<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadCapture\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\LeadCapture\Models\CapturedLead;
use Liberu\CRM\LeadCapture\Models\LeadCapture;
use Liberu\CRM\LeadCapture\Services\LeadCapturePolicy;

final class CaptureLead
{
    public function __construct(private readonly LeadCapturePolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): CapturedLead
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['external_key' => ['required', 'string', 'max:255'], 'channel' => ['required', 'in:manual,import,api,form,survey,qr,chat,call,advertisement,event,referral'], 'status' => ['nullable', 'in:new,contacted,qualified,converted,archived'], 'name' => ['nullable', 'string', 'max:255'], 'email' => ['nullable', 'email', 'max:255'], 'phone' => ['nullable', 'string', 'max:50'], 'source' => ['nullable', 'string', 'max:255'], 'source_metadata' => ['nullable', 'array'], 'payload' => ['nullable', 'array']])->validate();

        return CapturedLead::query()->updateOrCreate(['team_id' => $teamId, 'external_key' => $data['external_key']], ['channel' => $data['channel'], 'status' => $data['status'] ?? 'new', 'name' => $data['name'] ?? null, 'email' => $data['email'] ?? null, 'phone' => $data['phone'] ?? null, 'source' => $data['source'] ?? null, 'source_metadata' => $data['source_metadata'] ?? null, 'payload' => $data['payload'] ?? null]);
    }

    /** @param array<string, mixed> $input */
    public function executeLegacy(int $teamId, ?int $actorId, array $input): LeadCapture
    {
        $data = Validator::make($input, [
            'kind' => ['required', 'string', 'max:100'],
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'source' => ['nullable', 'string', 'max:255'],
            'payload' => ['nullable', 'array'],
            'provenance' => ['nullable', 'array'],
        ])->validate();

        return LeadCapture::query()->create([
            'team_id' => $teamId,
            'actor_id' => $actorId,
            'kind' => $data['kind'],
            'status' => 'new',
            'name' => $data['name'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'source' => $data['source'] ?? null,
            'payload' => $data['payload'] ?? null,
            'provenance' => $data['provenance'] ?? null,
            'captured_at' => now(),
        ]);
    }
}
