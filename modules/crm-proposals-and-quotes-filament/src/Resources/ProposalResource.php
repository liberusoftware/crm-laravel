<?php

declare(strict_types=1);

namespace Liberu\CRM\ProposalsAndQuotes\Filament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\ProposalsAndQuotes\Filament\Resources\ProposalResource\Pages\ListProposals;
use Liberu\CRM\ProposalsAndQuotes\Models\Proposal;

final class ProposalResource extends Resource
{
    protected static ?string $model = Proposal::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('title')->required(), TextInput::make('customer_id')->numeric(), Select::make('status')->options(['draft' => 'Draft', 'approved' => 'Approved', 'delivered' => 'Delivered', 'accepted' => 'Accepted', 'expired' => 'Expired']), TextInput::make('currency')->length(3)->required(), DatePicker::make('expires_at')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('title'), TextColumn::make('customer_id'), TextColumn::make('status')->badge(), TextColumn::make('total'), TextColumn::make('expires_at')->date()]);
    }

    public static function getEloquentQuery(): Builder
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return parent::getEloquentQuery()->where('team_id', $id);
    }

    public static function getPages(): array
    {
        return ['index' => ListProposals::route('/')];
    }
}
