<?php

declare(strict_types=1);

namespace Liberu\CRM\KnowledgeFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\KnowledgeFilament\Resources\KnowledgeArticleResource;

final class CreateKnowledgeArticle extends CreateRecord
{
    protected static string $resource = KnowledgeArticleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['team_id'] = (int) auth()->user()?->getAttribute('current_team_id');

        return $data;
    }
}
