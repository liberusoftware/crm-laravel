<?php

declare(strict_types=1);

namespace Liberu\CRM\DialerAndOutreach\Actions;

use App\Models\Contact;
use App\Models\User;
use App\Support\AccessContext;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Liberu\CRM\DialerAndOutreach\Models\DialerCall;
use Liberu\CRM\DialerAndOutreach\Models\DialerList;
use Liberu\CRM\DialerAndOutreach\Queries\ContactCallHistory;
use Liberu\CRM\DialerAndOutreach\Services\DialerPolicy;
use Liberu\CRM\Telephony\Models\TelephonySettings;

final class StartContactDialing
{
    public function available(User $user): bool
    {
        $teamId = (int) $user->getAttribute('current_team_id');
        if ($teamId < 1 || AccessContext::shouldMaskFields() || ! app(DialerPolicy::class)->canManage($teamId, (int) $user->id)) {
            return false;
        }
        $settings = TelephonySettings::query()->where('team_id', $teamId)->first();

        return filled($settings?->account_sid) && filled($settings?->auth_token) && filled($settings?->default_from_number);
    }

    /** @return array{list: DialerList, queued: int, skipped: int} */
    public function execute(User $user, array $contactIds, array $input, bool $quick = false): array
    {
        abort_unless($this->available($user), 403, 'Configure team VoIP and obtain dialing permission first.');
        $data = Validator::make($input, [
            'agent_number' => ['required', 'string', 'regex:/^\+[1-9][0-9]{7,14}$/'],
            'opening' => ['required', 'string', 'max:4000'],
            'cooldown_days' => ['required', 'integer', 'in:1,7,14'],
        ])->validate();
        Validator::make(['contacts' => $contactIds], ['contacts' => ['required', 'array', 'max:500'], 'contacts.*' => ['required', 'integer', 'distinct']])->validate();
        $teamId = (int) $user->getAttribute('current_team_id');

        return DB::transaction(function () use ($teamId, $user, $contactIds, $data, $quick): array {
            TelephonySettings::query()->where('team_id', $teamId)->lockForUpdate()->firstOrFail();
            $contacts = Contact::query()->where('team_id', $teamId)->whereKey($contactIds)->orderBy('id')->lockForUpdate()->get();
            abort_unless($contacts->count() === count($contactIds), 403);
            foreach ($contacts as $contact) {
                Gate::forUser($user)->authorize('update', $contact);
            }
            $list = app(CreateDialerList::class)->execute($teamId, (int) $user->id, [
                'name' => ($quick ? 'Quick call' : 'Selected contacts').' · '.now()->format('M j H:i'),
                'mode' => 'progressive',
                'compliance' => ['consent_required' => false, 'cooldown_days' => (int) $data['cooldown_days']],
                'script' => ['opening' => $data['opening'], 'agent_number' => $data['agent_number']],
            ]);
            $queued = 0;
            foreach ($contacts as $contact) {
                $phone = preg_replace('/[\s().-]+/', '', (string) $contact->getAttribute('phone_number'));
                if (! preg_match('/^\+[1-9][0-9]{7,14}$/', $phone)
                    || ! app(ContactCallHistory::class)->notCalledSince(Contact::query()->whereKey($contact->id), app(ContactCallHistory::class)->cutoff((int) $data['cooldown_days']))->exists()
                    || DialerCall::query()->where('team_id', $teamId)->where('phone', $phone)->where(function ($query): void {
                        $query->where('outcome', 'do_not_call')->orWhereIn('status', ['queued', 'dispatching', 'in_progress', 'dispatch_unknown']);
                    })->exists()) {
                    continue;
                }
                app(QueueDialerCall::class)->execute($teamId, (int) $user->id, $list, ['phone' => $phone, 'contact_id' => $contact->id]);
                $queued++;
            }
            $list->update(['status' => $queued > 0 ? 'active' : 'paused']);

            return ['list' => $list, 'queued' => $queued, 'skipped' => count($contactIds) - $queued];
        });
    }
}
