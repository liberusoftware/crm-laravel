<?php

namespace App\Services;

use App\Models\Team;
use App\Models\TeamSubscription;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class StripeService
{
    protected StripeClient $stripeClient;

    public function __construct()
    {
        $this->stripeClient = new StripeClient(config('services.stripe.secret'));
    }

    public function createSubscription(Team $team, string $paymentMethodId, string $plan = 'monthly'): TeamSubscription
    {
        if (! config('saas.enabled', false)) {
            throw new Exception('Subscriptions are currently disabled');
        }

        $planConfig = config("saas.plans.{$plan}");
        if (! is_array($planConfig)) {
            throw new Exception('The selected billing plan is not configured.');
        }
        $priceId = $this->resolvePriceId($plan, $planConfig);

        try {
            // Create or get Stripe customer
            $stripeCustomer = $this->getOrCreateCustomer($team);

            // Attach payment method to customer
            $this->stripeClient->paymentMethods->attach($paymentMethodId, [
                'customer' => $stripeCustomer->id,
            ]);

            // Set as default payment method
            $this->stripeClient->customers->update($stripeCustomer->id, [
                'invoice_settings' => [
                    'default_payment_method' => $paymentMethodId,
                ],
            ]);

            // Create subscription
            $subscription = $this->stripeClient->subscriptions->create([
                'customer' => $stripeCustomer->id,
                'items' => [[
                    'price' => $priceId,
                    'quantity' => 1,
                ]],
                'trial_period_days' => (int) config('saas.trial_days', 14),
                'payment_settings' => ['save_default_payment_method' => 'on_subscription'],
                'expand' => ['latest_invoice.payment_intent'],
            ]);

            // Create local subscription record
            return TeamSubscription::create([
                'team_id' => $team->id,
                'stripe_id' => $subscription->id,
                'stripe_status' => $subscription->status,
                'stripe_price' => $priceId,
                'plan_key' => $plan,
                'quantity' => 1,
                'trial_ends_at' => now()->addDays((int) config('saas.trial_days', 14)),
                'current_period_ends_at' => ($periodEnd = $subscription->toArray()['current_period_end'] ?? null) ? now()->setTimestamp((int) $periodEnd) : null,
                'ends_at' => null,
            ]);
        } catch (ApiErrorException $e) {
            throw new Exception('Failed to create subscription: '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    public function updatePaymentMethod(Team $team, string $paymentMethodId): void
    {
        if (! $team->stripe_id) {
            throw new Exception('No Stripe customer exists for this team.');
        }

        try {
            $this->stripeClient->paymentMethods->attach($paymentMethodId, ['customer' => $team->stripe_id]);
            $this->stripeClient->customers->update($team->stripe_id, ['invoice_settings' => ['default_payment_method' => $paymentMethodId]]);
        } catch (ApiErrorException $e) {
            throw new Exception('Failed to update payment details: '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    /** @param array<string, mixed> $planConfig */
    public function resolvePriceId(string $plan, array $planConfig): string
    {
        if (is_string($planConfig['price_id'] ?? null) && $planConfig['price_id'] !== '') {
            return $planConfig['price_id'];
        }
        if (! is_numeric($planConfig['amount'] ?? null) || ! in_array($planConfig['interval'] ?? null, ['month', 'year'], true)) {
            throw new Exception("Invalid Stripe configuration for plan [{$plan}].");
        }

        $namespace = Str::slug((string) config('saas.stripe_namespace', config('app.name', 'liberu-crm')));
        $cacheKey = "stripe-price:{$namespace}:{$plan}";

        return (string) Cache::rememberForever($cacheKey, function () use ($namespace, $plan, $planConfig): string {
            $products = $this->stripeClient->products->all(['limit' => 100, 'active' => true]);
            $product = collect($products->data)->first(function ($candidate) use ($namespace, $plan): bool {
                $metadata = $candidate->toArray()['metadata'] ?? [];

                return ($metadata['liberu_namespace'] ?? null) === $namespace && ($metadata['liberu_plan'] ?? null) === $plan;
            });
            if ($product === null) {
                $product = $this->stripeClient->products->create([
                    'name' => config('app.name').' '.ucfirst($plan),
                    'metadata' => ['liberu_namespace' => $namespace, 'liberu_plan' => $plan],
                ], ['idempotency_key' => "liberu-product-{$namespace}-{$plan}"]);
            }

            $amount = (int) round(((float) $planConfig['amount']) * 100);
            $productId = (string) ($product->toArray()['id'] ?? '');
            $prices = $this->stripeClient->prices->all(['product' => $productId, 'active' => true, 'limit' => 100]);
            $price = collect($prices->data)->first(function ($candidate) use ($amount, $planConfig): bool {
                $candidateData = $candidate->toArray();

                return ($candidateData['currency'] ?? null) === config('saas.currency')
                    && ($candidateData['unit_amount'] ?? null) === $amount
                    && (($candidateData['recurring']['interval'] ?? null) === $planConfig['interval']);
            });
            if ($price !== null) {
                return (string) ($price->toArray()['id'] ?? '');
            }

            $price = $this->stripeClient->prices->create([
                'product' => $productId,
                'currency' => config('saas.currency'),
                'unit_amount' => $amount,
                'recurring' => ['interval' => $planConfig['interval']],
                'metadata' => ['liberu_namespace' => $namespace, 'liberu_plan' => $plan],
            ], ['idempotency_key' => "liberu-price-{$namespace}-{$plan}-{$amount}"]);

            return (string) ($price->toArray()['id'] ?? '');
        });
    }

    public function cancelSubscription(TeamSubscription $subscription): void
    {
        try {
            $this->stripeClient->subscriptions->cancel($subscription->stripe_id, [
                'prorate' => true,
            ]);

            $subscription->update([
                'ends_at' => now(),
            ]);
        } catch (ApiErrorException $e) {
            throw new Exception('Failed to cancel subscription: '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    protected function getOrCreateCustomer(Team $team)
    {
        if ($team->stripe_id) {
            return $this->stripeClient->customers->retrieve($team->stripe_id);
        }

        $customer = $this->stripeClient->customers->create([
            'email' => $team->owner->email,
            'name' => $team->name,
            'metadata' => [
                'team_id' => $team->id,
            ],
        ]);

        $team->update(['stripe_id' => $customer->id]);

        return $customer;
    }

    public function updateSubscriptionQuantity(TeamSubscription $subscription, int $quantity): void
    {
        try {
            $this->stripeClient->subscriptions->update($subscription->stripe_id, [
                'items' => [
                    [
                        'price' => $subscription->stripe_price,
                        'quantity' => $quantity,
                    ],
                ],
            ]);

            $subscription->update(['quantity' => $quantity]);
        } catch (ApiErrorException $e) {
            throw new Exception('Failed to update subscription quantity: '.$e->getMessage(), $e->getCode(), $e);
        }
    }
}
