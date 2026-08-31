<?php

declare(strict_types=1);

namespace App\Enums;

enum SocialPlatform: string
{
    case Facebook = 'facebook';
    case Instagram = 'instagram';
    case LinkedIn = 'linkedin';
    case Twitter = 'twitter';
    case YouTube = 'youtube';
    case TikTok = 'tiktok';
    case Pinterest = 'pinterest';
    case Reddit = 'reddit';
    case Bluesky = 'bluesky';
    case Threads = 'threads';
    case GoogleBusiness = 'googlebusiness';
    case Telegram = 'telegram';

    public function label(): string
    {
        return match ($this) {
            self::Facebook => 'Facebook',
            self::Instagram => 'Instagram',
            self::LinkedIn => 'LinkedIn',
            self::Twitter => 'Twitter/X',
            self::YouTube => 'YouTube',
            self::TikTok => 'TikTok',
            self::Pinterest => 'Pinterest',
            self::Reddit => 'Reddit',
            self::Bluesky => 'Bluesky',
            self::Threads => 'Threads',
            self::GoogleBusiness => 'Google Business',
            self::Telegram => 'Telegram',
        };
    }

    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $platform) {
            $options[$platform->value] = $platform->label();
        }

        return $options;
    }

    public static function values(): array
    {
        return array_map(
            static fn (self $platform): string => $platform->value,
            self::cases(),
        );
    }
}
