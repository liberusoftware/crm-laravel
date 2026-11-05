<?php

declare(strict_types=1);

namespace Liberu\CRM\DialerAndOutreach\Actions;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Liberu\CRM\DialerAndOutreach\Models\DialerCall;
use Liberu\CRM\DialerAndOutreach\Models\DialerList;
use Liberu\CRM\DialerAndOutreach\Queries\ContactCallHistory;
use Liberu\CRM\DialerAndOutreach\Services\DialerPolicy;
use Liberu\CRM\Telephony\Models\TelephonySettings;
use Twilio\TwiML\VoiceResponse;

final class QueueDialerCall
{
    public function __construct(private readonly DialerPolicy $policy) {}

    public function execute(int $teamId, int $userId, DialerList $list, array $input): DialerCall
    {
        abort_unless($list->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['phone' => ['required', 'string', 'regex:/^\+[1-9][0-9]{7,14}$/'], 'contact_id' => ['nullable', 'integer'], 'consent' => ['nullable', 'boolean'], 'next_attempt_at' => ['nullable', 'date']])->validate();

        abort_unless(($list->compliance['consent_required'] ?? false) === false || (bool) ($data['consent'] ?? false), 422, 'Consent is required.');
        abort_if(DialerCall::query()->where('team_id', $teamId)->where('phone', $data['phone'])->where('outcome', 'do_not_call')->exists(), 422, 'This number has opted out.');

        $data['consent_granted'] = (bool) ($data['consent'] ?? false);
        unset($data['consent']);

        return DialerCall::query()->create(['team_id' => $teamId, 'list_id' => $list->id, 'status' => 'queued', ...$data]);
    }

    public function dispatch(int $teamId, int $userId, DialerList $list): ?DialerCall
    {
        abort_unless((int) $list->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        abort_unless($list->status === 'active', 422, 'Activate the list before dialing.');
        $script = Validator::make((array) $list->script, [
            'opening' => ['required', 'string', 'max:4000'],
            'agent_number' => ['required', 'string', 'regex:/^\\+[1-9][0-9]{7,14}$/'],
        ])->validate();
        $settings = TelephonySettings::query()->where('team_id', $teamId)->firstOrFail();
        abort_unless($settings->account_sid && $settings->auth_token && $settings->default_from_number, 422, 'Configure Twilio credentials and a sender number.');
        if (! empty($list->local_time_policy)) {
            $hours = Validator::make($list->local_time_policy, [
                'timezone' => ['required', 'timezone'],
                'start' => ['required', 'date_format:H:i'],
                'end' => ['required', 'date_format:H:i', 'after:start'],
                'days' => ['required', 'array', 'min:1'],
                'days.*' => ['integer', 'between:1,7'],
            ])->validate();
            $local = now($hours['timezone']);
            if (! in_array($local->isoWeekday(), array_map('intval', $hours['days']), true)
                || $local->format('H:i') < $hours['start'] || $local->format('H:i') >= $hours['end']) {
                return null;
            }
        }
        $call = DB::transaction(function () use ($teamId, $list): ?DialerCall {
            TelephonySettings::query()->where('team_id', $teamId)->lockForUpdate()->firstOrFail();
            $lockedList = DialerList::query()->whereKey($list->id)->lockForUpdate()->firstOrFail();
            abort_unless($lockedList->status === 'active', 422);
            if (DialerCall::query()->where('team_id', $teamId)->where('list_id', $list->id)->whereIn('status', ['dispatching', 'in_progress', 'dispatch_unknown'])->exists()) {
                return null;
            }
            $call = DialerCall::query()->where('team_id', $teamId)->where('list_id', $list->id)->where('status', 'queued')
                ->where('attempts', '<', 3)->where(function ($query): void {
                    $query->whereNull('next_attempt_at')->orWhere('next_attempt_at', '<=', now());
                })->orderBy('id')->lockForUpdate()->first();
            if ($call === null) {
                return null;
            }
            if (DialerCall::query()->where('team_id', $teamId)->where('phone', $call->phone)->where('outcome', 'do_not_call')->exists()) {
                $call->update(['status' => 'completed', 'outcome' => 'do_not_call', 'completed_at' => now()]);

                return null;
            }
            if (($lockedList->compliance['consent_required'] ?? false) && ! $call->consent_granted) {
                $call->update(['status' => 'blocked']);

                return null;
            }
            if ($call->contact_id !== null && isset($lockedList->compliance['cooldown_days'])) {
                $actor = User::query()->find($list->owner_id);
                $contact = Contact::query()->where('team_id', $teamId)->whereKey($call->contact_id)->lockForUpdate()->first();
                $history = app(ContactCallHistory::class);
                if ($actor === null || $contact === null || ! Gate::forUser($actor)->allows('update', $contact)
                    || ! $history->notCalledSince(Contact::query()->whereKey($contact->id), $history->cutoff((int) $lockedList->compliance['cooldown_days']))->exists()) {
                    $call->update(['status' => 'blocked']);

                    return null;
                }
            }
            $agentLists = DialerList::query()->where('team_id', $teamId)
                ->where('script->agent_number', $lockedList->script['agent_number'])->select('id');
            if (DialerCall::query()->where('team_id', $teamId)->whereIn('status', ['dispatching', 'in_progress', 'dispatch_unknown'])
                ->where(function ($query) use ($agentLists, $call): void {
                    $query->whereIn('list_id', $agentLists)->orWhere('phone', $call->phone);
                })->exists()) {
                return null;
            }
            $call->update(['status' => 'dispatching', 'attempts' => $call->attempts + 1, 'last_attempt_at' => now()]);

            return $call;
        });
        if ($call === null) {
            return null;
        }
        $twiml = new VoiceResponse();
        $twiml->say($script['opening']);
        $twiml->dial($script['agent_number']);
        try {
            $response = Http::asForm()->withBasicAuth($settings->account_sid, $settings->auth_token)->timeout(20)
                ->post('https://api.twilio.com/2010-04-01/Accounts/'.rawurlencode($settings->account_sid).'/Calls.json', [
                    'To' => $call->phone, 'From' => $settings->default_from_number, 'Twiml' => (string) $twiml,
                    'StatusCallback' => URL::signedRoute('crm.dialer.status', ['call' => $call->id, 'attempt' => $call->attempts]),
                ]);
            if (! $response->successful()) {
                if ($response->clientError()) {
                    $call->update(['status' => 'failed', 'outcome' => 'failed']);
                }
                throw new \RuntimeException('Twilio rejected the outbound call.');
            }
            $sid = $response->json('sid');
            if (! is_string($sid) || ! preg_match('/^CA[0-9a-fA-F]{32}$/', $sid)) {
                throw new \RuntimeException('Twilio returned an invalid call identifier.');
            }
            $call->update(['provider_sid' => $sid]);
            DialerCall::query()->whereKey($call->id)->where('status', 'dispatching')->update(['status' => 'in_progress']);
        } catch (\Throwable $exception) {
            DialerCall::query()->whereKey($call->id)->where('status', 'dispatching')->update(['status' => 'dispatch_unknown']);
            throw $exception;
        }

        return $call->refresh();
    }
}
