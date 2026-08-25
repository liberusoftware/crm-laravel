<?php

declare(strict_types=1);

namespace Tests\Feature\ReputationManagement;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\ReputationManagement\Actions\ConnectReviewSite;
use Liberu\CRM\ReputationManagement\Actions\CreateReviewRequest;
use Liberu\CRM\ReputationManagement\Actions\RecordReview;
use Liberu\CRM\ReputationManagement\Actions\RespondToReview;
use Liberu\CRM\ReputationManagement\Models\ReputationConnection;
use Liberu\CRM\ReputationManagement\Models\ReputationRequest;
use Liberu\CRM\ReputationManagement\Models\ReputationReview;
use Tests\TestCase;

final class ReputationManagementModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_review_connection_request_sentiment_and_response_are_team_scoped(): void
    {
        $owner = User::factory()->create();
        $other = Team::factory()->create(['user_id' => User::factory()->create()->id]);
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $connection = app(ConnectReviewSite::class)->execute($team->id, $owner->id, ['site' => 'google', 'location' => 'London', 'credentials' => ['token' => 'secret']]);
        $request = app(CreateReviewRequest::class)->execute($team->id, $owner->id, ['customer_id' => 42, 'connection_id' => $connection->id, 'channel' => 'email']);
        $review = app(RecordReview::class)->execute($team->id, $owner->id, ['connection_id' => $connection->id, 'customer_id' => 42, 'external_id' => 'review-1', 'rating' => 2, 'sentiment' => 'negative', 'content' => 'Needs attention']);
        app(RespondToReview::class)->execute($team->id, $owner->id, $review->id, ['response' => 'We are investigating this.']);

        self::assertSame('email', $request->channel);
        self::assertSame('responded', $review->refresh()->status);
        self::assertSame('negative', $review->sentiment);
        self::assertCount(1, ReputationConnection::query()->where('team_id', $team->id)->get());
        self::assertCount(0, ReputationRequest::query()->where('team_id', $other->id)->get());
        self::assertCount(0, ReputationReview::query()->where('team_id', $other->id)->get());
    }
}
