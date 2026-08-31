<?php

declare(strict_types=1);

namespace Liberu\CRM\KnowledgeFilament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\Knowledge\Models\KnowledgeArticle;
use Liberu\CRM\KnowledgeFilament\Resources\Pages\CreateKnowledgeArticle;
use Liberu\CRM\KnowledgeFilament\Resources\Pages\EditKnowledgeArticle;
use Liberu\CRM\KnowledgeFilament\Resources\Pages\ListKnowledgeArticles;

final class KnowledgeArticleResource extends Resource
{
    protected static ?string $model = KnowledgeArticle::class;

    protected static ?string $navigationLabel = 'Knowledge';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([]);
    }

    public static function getEloquentQuery(): Builder
    {
        $teamId = (int) auth()->user()?->current_team_id;

        abort_unless($teamId > 0, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListKnowledgeArticles::route('/'), 'create' => CreateKnowledgeArticle::route('/create'), 'edit' => EditKnowledgeArticle::route('/{record}/edit')];
    }
}
