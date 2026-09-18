<?php

declare(strict_types=1);

namespace Liberu\CRM\KnowledgeFilament\Resources;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
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

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required()->maxLength(255),
            TextInput::make('slug')->required()->maxLength(255)->helperText('Stable URL identifier for this article.'),
            Select::make('status')->options(['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'])->default('draft')->required(),
            Select::make('visibility')->options(['internal' => 'Internal', 'customer' => 'Customers', 'partner' => 'Partners', 'public' => 'Public'])->default('internal')->required(),
            TextInput::make('category')->maxLength(120),
            TextInput::make('locale')->default('en')->required()->maxLength(12),
            Textarea::make('body')->label('Article content')->required()->rows(16)->columnSpanFull(),
            DateTimePicker::make('reviewed_at'),
            DateTimePicker::make('stale_at'),
            KeyValue::make('metadata')->json()->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->searchable()->sortable(),
            TextColumn::make('category')->searchable(),
            TextColumn::make('visibility')->badge(),
            TextColumn::make('status')->badge(),
            TextColumn::make('locale')->sortable(),
            TextColumn::make('stale_at')->dateTime()->sortable(),
            TextColumn::make('updated_at')->dateTime()->sortable(),
        ])->defaultSort('updated_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $teamId = (int) auth()->user()?->getAttribute('current_team_id');

        abort_unless($teamId > 0, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListKnowledgeArticles::route('/'), 'create' => CreateKnowledgeArticle::route('/create'), 'edit' => EditKnowledgeArticle::route('/{record}/edit')];
    }
}
