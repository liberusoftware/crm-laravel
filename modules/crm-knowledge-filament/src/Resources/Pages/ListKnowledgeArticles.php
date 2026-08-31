<?php

declare(strict_types=1);

namespace Liberu\CRM\KnowledgeFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\KnowledgeFilament\Resources\KnowledgeArticleResource;

final class ListKnowledgeArticles extends ListRecords
{
    protected static string $resource = KnowledgeArticleResource::class;
}
