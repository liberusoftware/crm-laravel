<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualification\Filament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\LeadQualification\Filament\Resources\LeadQualificationResource\Pages\CreateLeadQualification;
use Liberu\CRM\LeadQualification\Filament\Resources\LeadQualificationResource\Pages\EditLeadQualification;
use Liberu\CRM\LeadQualification\Filament\Resources\LeadQualificationResource\Pages\ListLeadQualifications;
use Liberu\CRM\LeadQualification\Models\LeadQualification;

final class LeadQualificationResource extends Resource
{
    protected static ?string $model = LeadQualification::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-horizontal';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('subject_type')->required()->maxLength(160),
            TextInput::make('subject_id')->required()->numeric()->minValue(1),
            TextInput::make('framework_id')->numeric(),
            Select::make('lifecycle_stage')->options(['subscriber' => 'Subscriber', 'lead' => 'Lead', 'marketing_qualified' => 'Marketing qualified', 'product_qualified' => 'Product qualified', 'sales_qualified' => 'Sales qualified', 'service_qualified' => 'Service qualified', 'opportunity' => 'Opportunity', 'customer' => 'Customer'])->required(),
            TextInput::make('fit_score')->numeric()->minValue(0)->maxValue(100)->required(),
            TextInput::make('engagement_score')->numeric()->minValue(0)->maxValue(100)->required(),
            Select::make('qualification_status')->options(['unqualified' => 'Unqualified', 'marketing_qualified' => 'MQL', 'product_qualified' => 'PQL', 'sales_qualified' => 'SQL', 'service_qualified' => 'Service qualified', 'nurturing' => 'Nurturing', 'disqualified' => 'Disqualified', 'converted' => 'Converted'])->required(),
            Textarea::make('disqualification_reason')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('subject_type')->searchable(), TextColumn::make('subject_id'), TextColumn::make('lifecycle_stage')->badge(), TextColumn::make('total_score')->sortable(), TextColumn::make('qualification_status')->badge(), TextColumn::make('updated_at')->dateTime()]);
    }

    public static function getEloquentQuery(): Builder
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListLeadQualifications::route('/'), 'create' => CreateLeadQualification::route('/create'), 'edit' => EditLeadQualification::route('/{record}/edit')];
    }
}
