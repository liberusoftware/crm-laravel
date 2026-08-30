<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'gmail' => [
        'application_name' => env('GMAIL_APPLICATION_NAME'),
        'credentials_path' => env('GMAIL_CREDENTIALS_PATH'),
    ],

    'twilio' => [
        'account_sid' => env('TWILIO_ACCOUNT_SID', env('TWILIO_SID')),
        'auth_token' => env('TWILIO_AUTH_TOKEN', env('TWILIO_CLIENT_SECRET')),
        'phone_number' => env('TWILIO_PHONE_NUMBER'),
        'client_id' => env('TWILIO_CLIENT_ID'),
        'client_secret' => env('TWILIO_CLIENT_SECRET'),
        'redirect_uri' => env('TWILIO_REDIRECT_URI'),
        'sid' => env('TWILIO_SID'),
        'app_sid' => env('TWILIO_APP_SID'),
        'twiml_app_sid' => env('TWILIO_TWIML_APP_SID'),
        'webhook_url' => env('TWILIO_WEBHOOK_URL'),
    ],

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'subscriptions_enabled' => (bool) env('STRIPE_SUBSCRIPTIONS_ENABLED', false),
        'trial_days' => (int) env('STRIPE_TRIAL_DAYS', 14),
        'max_team_users' => (int) env('STRIPE_MAX_TEAM_USERS', 5),
        'price_id' => env('STRIPE_PRICE_ID'),
    ],

    // Facebook: unified config for both Socialite (OAuth login/connect) and Graph API (posting)
    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID', env('FACEBOOK_APP_ID')),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET', env('FACEBOOK_APP_SECRET')),
        'redirect' => env('APP_URL').'/oauth/facebook/callback',
        'app_id' => env('FACEBOOK_APP_ID', env('FACEBOOK_CLIENT_ID')),
        'app_secret' => env('FACEBOOK_APP_SECRET', env('FACEBOOK_CLIENT_SECRET')),
        'page_id' => env('FACEBOOK_PAGE_ID'),
        'page_access_token' => env('FACEBOOK_PAGE_ACCESS_TOKEN'),
        'graph_version' => env('FACEBOOK_GRAPH_VERSION', 'v18.0'),
    ],

    // Google: unified config for Socialite, Gmail, Google Ads, and YouTube
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('APP_URL').'/oauth/google/callback',
        'credentials_path' => env('GOOGLE_CREDENTIALS_PATH'),
        'developer_token' => env('GOOGLE_ADS_DEVELOPER_TOKEN'),
    ],

    // Twitter/X OAuth 2.0 for posting tweets, images, and videos
    'twitter-oauth-2' => [
        'client_id' => env('TWITTER_CLIENT_ID'),
        'client_secret' => env('TWITTER_CLIENT_SECRET'),
        'redirect' => env('APP_URL').'/oauth/twitter-oauth-2/callback',
    ],

    // LinkedIn for posting text, images, and videos
    'linkedin-openid' => [
        'client_id' => env('LINKEDIN_CLIENT_ID'),
        'client_secret' => env('LINKEDIN_CLIENT_SECRET'),
        'redirect' => env('APP_URL').'/oauth/linkedin-openid/callback',
    ],

    'mailchimp' => [
        'api_key' => env('MAILCHIMP_API_KEY'),
        'server_prefix' => env('MAILCHIMP_SERVER_PREFIX'),
    ],

    'whatsapp' => [
        'api_url' => env('WHATSAPP_API_URL'),
        'access_token' => env('WHATSAPP_ACCESS_TOKEN'),
    ],

    'zernio' => [
        'base_url' => env('ZERNIO_BASE_URL', 'https://zernio.com/api/v1'),
        'api_key' => env('ZERNIO_API_KEY'),
        'timeout' => (int) env('ZERNIO_TIMEOUT', 30),
        'retries' => (int) env('ZERNIO_RETRIES', 2),
        'mode' => env('ZERNIO_MODE', 'fallback'),
        'profile_id' => env('ZERNIO_PROFILE_ID'),
        'account_ids' => [
            'twitter' => env('ZERNIO_TWITTER_ACCOUNT_ID'),
            'instagram' => env('ZERNIO_INSTAGRAM_ACCOUNT_ID'),
            'facebook' => env('ZERNIO_FACEBOOK_ACCOUNT_ID'),
            'linkedin' => env('ZERNIO_LINKEDIN_ACCOUNT_ID'),
            'tiktok' => env('ZERNIO_TIKTOK_ACCOUNT_ID'),
            'youtube' => env('ZERNIO_YOUTUBE_ACCOUNT_ID'),
            'pinterest' => env('ZERNIO_PINTEREST_ACCOUNT_ID'),
            'reddit' => env('ZERNIO_REDDIT_ACCOUNT_ID'),
            'bluesky' => env('ZERNIO_BLUESKY_ACCOUNT_ID'),
            'threads' => env('ZERNIO_THREADS_ACCOUNT_ID'),
            'telegram' => env('ZERNIO_TELEGRAM_ACCOUNT_ID'),
            'whatsapp' => env('ZERNIO_WHATSAPP_ACCOUNT_ID'),
            'googlebusiness' => env('ZERNIO_GOOGLEBUSINESS_ACCOUNT_ID'),
            'snapchat' => env('ZERNIO_SNAPCHAT_ACCOUNT_ID'),
            'discord' => env('ZERNIO_DISCORD_ACCOUNT_ID'),
            'slack' => env('ZERNIO_SLACK_ACCOUNT_ID'),
        ],
    ],

    'quickbooks' => [
        'client_id' => env('QUICKBOOKS_CLIENT_ID'),
        'client_secret' => env('QUICKBOOKS_CLIENT_SECRET'),
        'redirect_uri' => env('QUICKBOOKS_REDIRECT_URI'),
    ],

    'xero' => [
        'client_id' => env('XERO_CLIENT_ID'),
        'client_secret' => env('XERO_CLIENT_SECRET'),
        'redirect_uri' => env('XERO_REDIRECT_URI'),
    ],

    'outlook' => [
        'client_id' => env('OUTLOOK_CLIENT_ID'),
        'client_secret' => env('OUTLOOK_CLIENT_SECRET'),
        'redirect_uri' => env('OUTLOOK_REDIRECT_URI'),
    ],

    'microsoft' => [
        'client_id' => env('MICROSOFT_CLIENT_ID'),
        'client_secret' => env('MICROSOFT_CLIENT_SECRET'),
        'redirect' => env('APP_URL').'/oauth/microsoft/callback',
    ],

    'youtube' => [
        'client_id' => env('YOUTUBE_CLIENT_ID'),
        'client_secret' => env('YOUTUBE_CLIENT_SECRET'),
        'redirect' => env('APP_URL').'/oauth/youtube/callback',
    ],

    'imap' => [
        'host' => env('IMAP_HOST'),
        'port' => env('IMAP_PORT', 993),
        'username' => env('IMAP_USERNAME'),
        'password' => env('IMAP_PASSWORD'),
        'ssl' => env('IMAP_SSL', true),
        'smtp_host' => env('SMTP_HOST'),
        'smtp_port' => env('SMTP_PORT', 587),
    ],

    'pop3' => [
        'host' => env('POP3_HOST'),
        'port' => env('POP3_PORT', 110),
        'username' => env('POP3_USERNAME'),
        'password' => env('POP3_PASSWORD'),
        'ssl' => env('POP3_SSL', false),
        'smtp_host' => env('SMTP_HOST'),
        'smtp_port' => env('SMTP_PORT', 587),
    ],

];
