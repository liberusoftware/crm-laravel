<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Models\Contact;
use App\Models\Lead;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Liberu\CRM\DialerAndOutreach\Actions\QueueDialerCall;
use Liberu\CRM\DialerAndOutreach\Actions\SetDialerListStatus;
use Liberu\CRM\DialerAndOutreach\Actions\StartContactDialing;
use Liberu\CRM\DialerAndOutreach\Models\DialerCall;
use Liberu\CRM\DialerAndOutreach\Models\DialerList;
use Liberu\CRM\DialerAndOutreach\Queries\ContactCallHistory;

final class ContactDialingActions
{
    public static function quick(): Action
    {
        return Action::make('makeCall')
            ->label('Call contact')
            ->icon('heroicon-o-phone')
            ->visible(fn (Contact|Lead $record): bool => auth()->user() !== null
                && app(StartContactDialing::class)->available(auth()->user())
                && filled(($record instanceof Lead ? $record->contact : $record)?->getAttribute('phone_number')))
            ->modalDescription('Connect this contact to your agent number through team VoIP. Recent calls and contacts already in a dialing queue are skipped.')
            ->schema(self::fields())
            ->action(function (Contact|Lead $record, array $data): void {
                $user = auth()->user();
                abort_unless($user, 403);
                if ($record instanceof Lead) {
                    Gate::authorize('update', $record);
                }
                $contact = $record instanceof Lead ? $record->contact : $record;
                abort_unless($contact !== null, 422, 'Link a contact before calling.');
                $result = app(StartContactDialing::class)->execute($user, [$contact->getKey()], $data, quick: true);
                if ($result['queued'] === 0) {
                    Notification::make()->title('Contact skipped')->body('Recently called, already queued, opted out, or missing an international phone number.')->warning()->send();

                    return;
                }
                try {
                    $call = app(QueueDialerCall::class)->dispatch((int) $user->getAttribute('current_team_id'), (int) $user->id, $result['list']);
                    $queued = DialerCall::query()
                        ->where('team_id', $user->getAttribute('current_team_id'))->where('list_id', $result['list']->id)->where('status', 'queued')->exists();
                    Notification::make()->title($call ? 'Call started' : ($queued ? 'Call queued until your agent is available' : 'Call skipped after eligibility check'))->send();
                } catch (\Throwable $exception) {
                    report($exception);
                    Notification::make()->title('Call could not be confirmed')->body('Check the dialer call status before retrying.')->danger()->send();
                }
            });
    }

    public static function bulk(): BulkAction
    {
        return BulkAction::make('bulkMakeCall')
            ->label('Auto dial selected')
            ->icon('heroicon-o-phone')
            ->visible(fn (): bool => auth()->user() !== null && app(StartContactDialing::class)->available(auth()->user()))
            ->modalDescription('Queue up to 500 selected contacts. The dialer calls one contact at a time and connects answered calls to your agent number. Invalid, recently called, opted-out and already queued numbers are skipped.')
            ->schema(self::fields())
            ->action(function (Collection $records, array $data): void {
                $user = auth()->user();
                abort_unless($user, 403);
                $result = app(StartContactDialing::class)->execute($user, $records->pluck('id')->all(), $data);
                Notification::make()->title($result['queued'].' contacts queued; '.$result['skipped'].' skipped')
                    ->body('The auto dialer advances through the list while the scheduler is running.')->success()->send();
            })
            ->deselectRecordsAfterCompletion();
    }

    public static function manage(): Action
    {
        return Action::make('manageDialing')
            ->label('Manage auto dialing')
            ->icon('heroicon-o-pause-circle')
            ->visible(fn (): bool => auth()->user() !== null && app(StartContactDialing::class)->available(auth()->user()))
            ->modalDescription('Pause stops new calls; a call already in progress continues. Resume continues with the next eligible contact.')
            ->schema([
                Select::make('list_id')->label('Dialing list')->required()->searchable()
                    ->options(fn (): array => DialerList::query()
                        ->where('team_id', auth()->user()?->getAttribute('current_team_id'))->where('mode', 'progressive')->latest()->limit(100)->get()
                        ->mapWithKeys(fn ($list): array => [$list->id => $list->getAttribute('name').' · '.$list->status])->all()),
                Select::make('status')->label('Action')->required()->options(['paused' => 'Pause list', 'active' => 'Resume list'])->default('paused'),
            ])
            ->action(function (array $data): void {
                $user = auth()->user();
                abort_unless($user, 403);
                app(SetDialerListStatus::class)->execute((int) $user->getAttribute('current_team_id'), (int) $user->id, (int) $data['list_id'], $data['status']);
                Notification::make()->title('Dialing list updated')->success()->send();
            });
    }

    public static function lastCalledFilter(): SelectFilter
    {
        return SelectFilter::make('last_called')
            ->label('Last called')
            ->options(['never' => 'Never called', '1' => 'Not called today', '7' => 'Not called in 7 days', '14' => 'Not called in 14 days'])
            ->query(function (Builder $query, array $data): Builder {
                $value = $data['value'] ?? null;
                if (! in_array((string) $value, ['never', '1', '7', '14'], true)) {
                    return $query;
                }
                $history = app(ContactCallHistory::class);
                $since = $value === 'never' ? null : $history->cutoff((int) $value);
                if ($query->getModel() instanceof Lead) {
                    return $query->whereHas('contact', fn ($contacts) => $history->notCalledSince($contacts, $since));
                }

                return $history->notCalledSince($query, $since);
            });
    }

    private static function fields(): array
    {
        return [
            TextInput::make('agent_number')->label('Your agent number')->tel()->required()
                ->default(fn (): ?string => DialerList::query()
                    ->where('team_id', auth()->user()?->getAttribute('current_team_id'))
                    ->where('owner_id', auth()->id())->latest('id')->first()?->script['agent_number'] ?? null)
                ->regex('/^\+[1-9][0-9]{7,14}$/')->placeholder('+441234567890')
                ->helperText('The phone or VoIP number that receives connected calls, in international format.'),
            Textarea::make('opening')->label('Connection greeting')->required()->maxLength(4000)
                ->default('Please hold while we connect you to our team.'),
            Select::make('cooldown_days')->label('Skip contacts called')->required()->default(1)
                ->options([1 => 'Today', 7 => 'In the last 7 days', 14 => 'In the last 14 days']),
        ];
    }
}
