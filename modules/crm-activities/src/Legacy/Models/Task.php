<?php

namespace App\Models;

use App\Contracts\OwnsRecords;
use App\Services\RecurringTaskService;
use App\Traits\IsTenantModel;
use App\Traits\RestrictsToOwner;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Task extends Model implements OwnsRecords
{
    use HasFactory;
    use IsTenantModel;
    use RestrictsToOwner;

    /** Record-level ownership keys off the assignee, not a creator. */
    protected $ownerColumn = 'assigned_to';

    protected $primaryKey = 'id';

    protected $fillable = [
        'team_id',
        'name',
        'description',
        'due_date',
        'status',
        'recurrence',
        'completed_at',
        'contact_id',
        'lead_id',
        'company_id',
        'opportunity_id',
        'reminder_date',
        'reminder_sent',
        'google_event_id',
        'outlook_event_id',
        'calendar_type',
        'assigned_to',
        'overdue_notified',
    ];

    protected $casts = [
        'reminder_date' => 'datetime',
        'reminder_sent' => 'boolean',
        'due_date' => 'datetime',
        'completed_at' => 'datetime',
        'overdue_notified' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $task): void {
            if ($task->status === 'completed') {
                $task->completed_at ??= now();
            } elseif ($task->isDirty('status')) {
                $task->completed_at = null;
            }
        });

        static::updated(function (self $task): void {
            if (
                ! $task->wasChanged('status')
                || $task->status !== 'completed'
                || $task->recurrence === null
            ) {
                return;
            }

            app(RecurringTaskService::class)->createNextOccurrence($task);
        });
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(Opportunity::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function syncWithCalendar(): void
    {
        $calendarService = $this->getCalendarService();
        if ($calendarService) {
            if ($this->google_event_id || $this->outlook_event_id) {
                $calendarService->updateEvent($this);
            } else {
                $calendarService->createEvent($this);
            }
        }
    }

    public function deleteFromCalendar(): void
    {
        $calendarService = $this->getCalendarService();
        if ($calendarService && ($this->google_event_id || $this->outlook_event_id)) {
            $calendarService->deleteEvent($this);
        }
    }

    protected function getCalendarService()
    {
        if ($this->calendar_type === 'google') {
            return app(GoogleCalendarService::class);
        } elseif ($this->calendar_type === 'outlook') {
            return app(OutlookCalendarService::class);
        }

    }

    public function assign(User $user): void
    {
        $this->assigned_to = $user->id;
        $this->save();
    }

    public function markAsComplete(): void
    {
        $this->status = 'completed';
        $this->save();
    }

    public function markAsIncomplete(): void
    {
        $this->status = 'pending';
        $this->save();
    }

    public function isOverdue(): bool
    {
        $dueDate = $this->getAttribute('due_date');

        if (! $dueDate instanceof Carbon) {
            return false;
        }

        return $dueDate->isPast() && $this->status !== 'completed';
    }
}
