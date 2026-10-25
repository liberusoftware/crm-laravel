<?php

namespace App\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Webhook extends Model
{
    use HasFactory;
    use IsTenantModel;

    public const EVENTS = [
        'contact.created', 'contact.updated', 'contact.deleted',
        'company.created', 'company.updated', 'company.deleted',
        'lead.created', 'lead.updated', 'lead.converted', 'lead.deleted',
        'deal.created', 'deal.updated', 'deal.won', 'deal.lost', 'deal.deleted',
        'opportunity.created', 'opportunity.updated', 'opportunity.won', 'opportunity.lost',
        'task.created', 'task.updated', 'task.completed', 'task.deleted',
        'ticket.created', 'ticket.updated', 'ticket.closed',
    ];

    protected $fillable = ['name', 'url', 'events', 'secret', 'is_active', 'team_id', 'user_id', 'failure_count', 'last_triggered_at'];

    protected $hidden = ['secret'];

    protected $attributes = ['is_active' => true, 'failure_count' => 0];

    protected function casts(): array
    {
        return ['events' => 'array', 'secret' => 'encrypted', 'is_active' => 'boolean', 'team_id' => 'integer', 'failure_count' => 'integer', 'last_triggered_at' => 'datetime'];
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(WebhookDelivery::class);
    }
}
