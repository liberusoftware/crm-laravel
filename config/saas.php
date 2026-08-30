<?php

return [
    'enabled' => (bool) env('SAAS_BILLING_ENABLED', false),
    'trial_days' => (int) env('SAAS_TRIAL_DAYS', 14),
    'currency' => strtolower((string) env('SAAS_CURRENCY', 'gbp')),
    'stripe_namespace' => (string) env('SAAS_STRIPE_PRODUCT_NAMESPACE', env('APP_NAME', 'liberu-crm')),
    'plans' => [
        'monthly' => ['price_id' => env('STRIPE_PRICE_MONTHLY'), 'amount' => 19.99, 'interval' => 'month'],
        'yearly' => ['price_id' => env('STRIPE_PRICE_YEARLY'), 'amount' => 199.99, 'interval' => 'year'],
    ],
    'lock_statuses' => ['past_due', 'unpaid', 'incomplete_expired', 'payment_failed'],
];
