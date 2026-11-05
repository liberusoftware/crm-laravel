<?php

namespace App\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property string $content
 * @property string $status
 * @property array $platforms
 * @property array|null $platform_post_ids
 * @property string|null $link
 * @property string|null $image_path
 * @property string|null $video_url
 * @property Carbon|null $scheduled_at
 * @property Carbon|null $approved_at
 * @property int|null $approved_by
 */
class SocialMediaPost extends Model
{
    use HasFactory;
    use IsTenantModel;

    protected $fillable = [
        'publishing_provider', 'publishing_started_at', 'last_error', 'approved_at', 'approved_by',
        'content',
        'team_id',
        'link',
        'image_path',
        'video_url',
        'scheduled_at',
        'platforms',
        'status',
        'link',
        'image',
        'video',
        'platform_post_ids',
        'likes',
        'shares',
        'comments',
        'clicks',
        'reach',
        'impressions',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'approved_at' => 'datetime',
        'publishing_started_at' => 'datetime',
        'platforms' => 'array',
        'platform_post_ids' => 'array',
        'likes' => 'integer',
        'shares' => 'integer',
        'comments' => 'integer',
        'clicks' => 'integer',
        'reach' => 'integer',
        'impressions' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $post): void {
            if ($post->exists && $post->isDirty(['content', 'platforms', 'link', 'image_path', 'video_url'])) {
                $post->approved_at = null;
                $post->approved_by = null;
            }
        });
    }

    const STATUS_DRAFT = 'draft';

    const STATUS_SCHEDULED = 'scheduled';

    const STATUS_PUBLISHED = 'published';

    const STATUS_FAILED = 'failed';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SCHEDULED => 'Scheduled',
            self::STATUS_PUBLISHED => 'Published',
            self::STATUS_FAILED => 'Failed',
            'publishing' => 'Publishing',
            'publishing_unknown' => 'Needs reconciliation',
            'cancelled' => 'Cancelled',
        ];
    }

    public function isScheduled(): bool
    {
        return $this->status === self::STATUS_SCHEDULED && $this->scheduled_at > now();
    }

    public function isPublishable(): bool
    {
        return $this->status === self::STATUS_SCHEDULED && $this->scheduled_at <= now();
    }

    public function markAsPublished(): void
    {
        $this->status = self::STATUS_PUBLISHED;
        $this->save();
    }

    public function markAsFailed(): void
    {
        $this->status = self::STATUS_FAILED;
        $this->save();
    }
}
