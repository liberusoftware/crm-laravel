<?php

declare(strict_types=1);

namespace Liberu\CRM\KnowledgeFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\KnowledgeFilament\Resources\KnowledgeArticleResource;

final class CreateKnowledgeArticle extends CreateRecord
{
    protected static string $resource = KnowledgeArticleResource::class;
}
