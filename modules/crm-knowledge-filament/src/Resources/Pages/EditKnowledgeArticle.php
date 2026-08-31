<?php

declare(strict_types=1);

namespace Liberu\CRM\KnowledgeFilament\Resources\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\KnowledgeFilament\Resources\KnowledgeArticleResource;

final class EditKnowledgeArticle extends EditRecord
{
    protected static string $resource = KnowledgeArticleResource::class;
}
