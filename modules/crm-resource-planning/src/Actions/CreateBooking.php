<?php

declare(strict_types=1);

namespace Liberu\CRM\ResourcePlanning\Actions;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Liberu\CRM\ResourcePlanning\Events\ResourceBookingChanged;
use Liberu\CRM\ResourcePlanning\Models\ResourceBooking;
use Liberu\CRM\ResourcePlanning\Models\ResourceCapacity;
use Liberu\CRM\ResourcePlanning\Services\ResourcePlanningPolicy;

final class CreateBooking
{
    public function __construct(private readonly ResourcePlanningPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): ResourceBooking
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['resource_id' => ['required', 'integer'], 'subject_type' => ['required', 'string', 'max:255'], 'subject_id' => ['required', 'integer'], 'starts_at' => ['required', 'date'], 'ends_at' => ['required', 'date', 'after:starts_at'], 'status' => ['required', 'in:tentative,confirmed,cancelled'], 'rate' => ['nullable', 'numeric', 'min:0'], 'metadata' => ['nullable', 'array']])->validate();
        $starts = Carbon::parse($data['starts_at']);
        $ends = Carbon::parse($data['ends_at']);
        $hours = $starts->diffInMinutes($ends) / 60;
        $conflict = ResourceBooking::query()->where('team_id', $teamId)->where('resource_id', $data['resource_id'])->whereIn('status', ['tentative', 'confirmed'])->get()->contains(static fn (ResourceBooking $booking): bool => $booking->starts_at < $ends && $booking->ends_at > $starts);
        abort_if($conflict, 422, 'The resource has a conflicting booking.');
        $capacity = ResourceCapacity::query()->where('team_id', $teamId)->where('resource_id', $data['resource_id'])->whereDate('period_start', '<=', $starts)->whereDate('period_end', '>=', $ends)->first();
        abort_if($capacity !== null && ((float) $capacity->allocated_hours + $hours) > (float) $capacity->available_hours, 422, 'The booking exceeds available capacity.');
        $booking = ResourceBooking::query()->create(['team_id' => $teamId, ...$data, 'hours' => $hours]);
        if ($capacity !== null) {
            $capacity->increment('allocated_hours', $hours);
        }
        event(new ResourceBookingChanged($booking, 'created'));

        return $booking;
    }
}
