<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesWorkspace\Filament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\SalesWorkspace\Filament\Resources\WorkspaceItemResource\Pages\ListWorkspaceItems;
use Liberu\CRM\SalesWorkspace\Models\WorkspaceItem;

final class WorkspaceItemResource extends Resource
{
    protected static ?string $model = WorkspaceItem::class;

    public static function form(Schema $s): Schema
    {
        return $s->components([TextInput::make('title')->required(), Select::make('kind')->options(['lead' => 'Lead', 'deal' => 'Deal', 'task' => 'Task', 'follow_up' => 'Follow up'])->required(), Select::make('priority')->options(['low' => 'Low', 'normal' => 'Normal', 'high' => 'High', 'urgent' => 'Urgent']), Select::make('status')->options(['open' => 'Open', 'completed' => 'Completed', 'closed' => 'Closed']), TextInput::make('next_action')]);
    }

    public static function table(Table $t): Table
    {
        return $t->columns([TextColumn::make('title')->searchable(), TextColumn::make('kind')->badge(), TextColumn::make('priority')->badge(), TextColumn::make('status')->badge(), TextColumn::make('due_at')->dateTime()]);
    }

    public static function getEloquentQuery(): Builder
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return parent::getEloquentQuery()->where('team_id', $id);
    }

    public static function getPages(): array
    {
        return ['index' => ListWorkspaceItems::route('/')];
    }
}
