<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\SocialMediaPostResource\Pages;

use App\Filament\App\Resources\SocialMediaPostResource;
use App\Services\Marketing\PostWorkflow;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateSocialMediaPost extends CreateRecord
{
    protected static string $resource = SocialMediaPostResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return app(PostWorkflow::class)->prepare(
            Filament::auth()->user(), Filament::getTenant(), array_replace($data, ['status' => 'draft']), 'create_social_media_post',
        );
    }
}
