<?php

declare(strict_types=1);

namespace Liberu\CRM\DialerAndOutreach\Queries;

use App\Models\Contact;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

final class ContactCallHistory
{
    private const SOURCES = [
        'call_logs' => 'created_at',
        'crm_telephony_calls' => 'COALESCE(started_at, created_at)',
        'crm_dialer_calls' => 'last_attempt_at',
    ];

    public function withLastCalled(Builder $query): Builder
    {
        $query->addSelect('contacts.*');
        foreach (self::SOURCES as $table => $timestamp) {
            $query->selectSub(DB::table($table)->selectRaw('MAX('.$timestamp.')')
                ->whereColumn($table.'.contact_id', 'contacts.id')
                ->whereColumn($table.'.team_id', 'contacts.team_id'), $table.'_last_called');
        }

        return $query;
    }

    public function lastCalled(Contact $contact): ?string
    {
        $times = [];
        foreach (self::SOURCES as $table => $timestamp) {
            $key = $table.'_last_called';
            $times[] = array_key_exists($key, $contact->getAttributes())
                ? $contact->getAttribute($key)
                : DB::table($table)->where('team_id', $contact->getAttribute('team_id'))->where('contact_id', $contact->id)->selectRaw('MAX('.$timestamp.') AS last_called')->value('last_called');
        }

        return collect($times)->filter()->max();
    }

    public function notCalledSince(Builder $query, ?CarbonInterface $since): Builder
    {
        foreach (self::SOURCES as $table => $timestamp) {
            $query->whereNotExists(function ($history) use ($table, $timestamp, $since): void {
                $history->selectRaw('1')->from($table)
                    ->whereColumn($table.'.contact_id', 'contacts.id')
                    ->whereColumn($table.'.team_id', 'contacts.team_id')
                    ->whereRaw($timestamp.' IS NOT NULL');
                if ($since !== null) {
                    $history->whereRaw($timestamp.' >= ?', [$since]);
                }
            });
        }

        return $query;
    }

    public function cutoff(int $days): CarbonInterface
    {
        return $days === 1 ? now()->startOfDay() : now()->subDays($days);
    }
}
