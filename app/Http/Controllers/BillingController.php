<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\TeamSubscription;
use App\Models\User;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Throwable;

final class BillingController extends Controller
{
    public function subscribe(Request $request, StripeService $stripe): Response
    {
        $data = $request->validate(['plan' => ['required', 'in:monthly,yearly'], 'payment_method_id' => ['required', 'string', 'max:255']]);
        $team = $this->managedTeam($request);
        $stripe->createSubscription($team, $data['payment_method_id'], $data['plan']);

        return response(['status' => 'trialing', 'trial_days' => config('saas.trial_days')], 201);
    }

    public function updatePaymentMethod(Request $request, StripeService $stripe): Response
    {
        $data = $request->validate(['payment_method_id' => ['required', 'string', 'max:255']]);
        $stripe->updatePaymentMethod($this->managedTeam($request), $data['payment_method_id']);

        return response(['status' => 'updated']);
    }

    public function cancel(Request $request, StripeService $stripe): Response
    {
        $team = $this->managedTeam($request);
        $subscription = $team->subscription;
        abort_unless($subscription, 404);
        $stripe->cancelSubscription($subscription);

        return response(['status' => 'cancelled']);
    }

    public function webhook(Request $request): Response
    {
        try {
            $event = Webhook::constructEvent((string) $request->getContent(), (string) $request->header('Stripe-Signature'), (string) config('services.stripe.webhook_secret'));
            $object = $event->data->object;
            $subscription = TeamSubscription::query()->where('stripe_id', $object->id ?? $object->subscription ?? null)->first();
            if (! $subscription) {
                return response(['received' => true]);
            }

            $status = match ($event->type) {
                'invoice.payment_failed' => 'payment_failed',
                'invoice.payment_succeeded' => 'active',
                'customer.subscription.deleted' => 'cancelled',
                default => $object->status ?? $subscription->stripe_status,
            };
            $subscription->update([
                'stripe_status' => $status,
                'current_period_ends_at' => isset($object->current_period_end) ? now()->setTimestamp((int) $object->current_period_end) : $subscription->current_period_ends_at,
                'ends_at' => $status === 'cancelled' ? now() : null,
            ]);

            return response(['received' => true]);
        } catch (Throwable $e) {
            Log::warning('Invalid Stripe webhook.', ['error' => $e->getMessage()]);

            return response(['message' => 'Invalid webhook.'], 400);
        }
    }

    public function export(Request $request): Response
    {
        $team = $this->team($request);
        $rows = $team->contacts()->select(['id', 'name', 'email', 'phone_number'])->cursor();
        $csv = fopen('php://temp', 'w+');
        fputcsv($csv, ['id', 'name', 'email', 'phone']);
        foreach ($rows as $contact) {
            fputcsv($csv, [$contact->id, $contact->name, $contact->email, $contact->phone_number]);
        }
        rewind($csv);

        return response(stream_get_contents($csv), 200, ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="crm-data-export.csv"']);
    }

    private function team(Request $request): Team
    {
        $user = $request->user();
        $team = $user instanceof User ? $user->currentTeam : null;
        abort_unless($team instanceof Team, 403);

        return $team;
    }

    private function managedTeam(Request $request): Team
    {
        $team = $this->team($request);
        $user = $request->user();

        abort_unless(
            $user instanceof User
                && ($team->user_id === $user->id
                    || $team->users()->whereKey($user->id)->wherePivotIn('role', ['owner', 'admin', 'manager'])->exists()),
            403,
        );

        return $team;
    }
}
