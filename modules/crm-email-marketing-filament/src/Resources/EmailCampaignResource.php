<?php

declare(strict_types=1);

namespace Liberu\CRM\EmailMarketingFilament\Resources;

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
use Liberu\CRM\EmailMarketing\Models\EmailCampaign;
use Liberu\CRM\EmailMarketingFilament\Resources\Pages\CreateEmailCampaign;
use Liberu\CRM\EmailMarketingFilament\Resources\Pages\EditEmailCampaign;
use Liberu\CRM\EmailMarketingFilament\Resources\Pages\ListEmailCampaigns;

final class EmailCampaignResource extends Resource
{
    protected static ?string $model = EmailCampaign::class;

    protected static ?string $navigationLabel = 'Email campaigns';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-megaphone';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(160),
            Select::make('status')->options(['draft' => 'Draft', 'scheduled' => 'Scheduled', 'paused' => 'Paused', 'sent' => 'Sent', 'archived' => 'Archived'])->default('draft')->required(),
            Select::make('content_type')->options(['drag_and_drop' => 'Drag and drop', 'code' => 'HTML/code'])->default('code')->required(),
            TextInput::make('subject')->required()->maxLength(255),
            Textarea::make('content')->label('Email content')->required()->rows(14)->columnSpanFull(),
            KeyValue::make('personalization')->json()->label('Personalization mappings')->columnSpanFull(),
            KeyValue::make('dynamic_content')->json()->label('Dynamic content')->columnSpanFull(),
            KeyValue::make('deliverability')->json()->label('Deliverability settings')->columnSpanFull(),
            TextInput::make('throttle_per_minute')->numeric()->minValue(1),
            DateTimePicker::make('scheduled_at')->label('Schedule send'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('subject')->limit(50)->searchable(),
            TextColumn::make('content_type')->badge(),
            TextColumn::make('status')->badge(),
            TextColumn::make('scheduled_at')->dateTime()->sortable(),
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
        return ['index' => ListEmailCampaigns::route('/'), 'create' => CreateEmailCampaign::route('/create'), 'edit' => EditEmailCampaign::route('/{record}/edit')];
    }
}
