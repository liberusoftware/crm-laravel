<?php

declare(strict_types=1);

namespace Liberu\CRM\AutomationPackFilament\Resources;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\AutomationPack\Models\AutomationRecipe;
use Liberu\CRM\AutomationPackFilament\Resources\Pages\CreateAutomationRecipe;
use Liberu\CRM\AutomationPackFilament\Resources\Pages\EditAutomationRecipe;
use Liberu\CRM\AutomationPackFilament\Resources\Pages\ListAutomationRecipes;

final class AutomationRecipeResource extends Resource
{
    protected static ?string $model = AutomationRecipe::class;

    protected static ?string $navigationLabel = 'Automation recipes';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bolt';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(160),
            Select::make('status')->options(['draft' => 'Draft', 'active' => 'Active', 'paused' => 'Paused'])->default('draft')->required(),
            Toggle::make('approval_required')->label('Require approval before publishing')->default(true),
            Repeater::make('triggers')
                ->label('When should this run?')
                ->schema([
                    Select::make('event')->options([
                        'contact.created' => 'Contact created',
                        'contact.updated' => 'Contact updated',
                        'lead.created' => 'Lead created',
                        'deal.stage_changed' => 'Deal stage changed',
                        'email.opened' => 'Email opened',
                        'form.submitted' => 'Form submitted',
                        'task.completed' => 'Task completed',
                    ])->searchable()->required(),
                    TextInput::make('description')->label('Optional note')->maxLength(255),
                ])->columns(2)->minItems(1)->defaultItems(1)->required(),
            Repeater::make('conditions')
                ->label('Only continue when')
                ->schema([
                    TextInput::make('field')->placeholder('lead.score')->required()->maxLength(160),
                    Select::make('operator')->options(['equals' => 'Equals', 'not_equals' => 'Does not equal', 'contains' => 'Contains', 'greater_than' => 'Greater than', 'less_than' => 'Less than', 'is_set' => 'Is set', 'is_not_set' => 'Is not set'])->required(),
                    TextInput::make('value')->placeholder('50')->maxLength(255),
                ])->columns(3)->addActionLabel('Add condition')->collapsed(),
            Repeater::make('actions')
                ->label('Then do this')
                ->schema([
                    Select::make('type')->options([
                        'send_email' => 'Send email',
                        'send_sms' => 'Send SMS',
                        'update_contact' => 'Update contact',
                        'update_deal' => 'Update deal',
                        'create_task' => 'Create task',
                        'create_deal' => 'Create deal',
                        'add_tag' => 'Add tag',
                        'remove_tag' => 'Remove tag',
                        'assign_to_user' => 'Assign to user',
                        'webhook' => 'Call webhook',
                        'delay' => 'Wait before continuing',
                    ])->searchable()->required(),
                    TextInput::make('title')->label('Action title')->required()->maxLength(160),
                    KeyValue::make('configuration')->label('Action configuration')->keyLabel('Setting')->valueLabel('Value')->json(),
                ])->columns(2)->minItems(1)->defaultItems(1)->reorderable()->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('status')->badge(),
            TextColumn::make('version')->numeric()->sortable(),
            TextColumn::make('approval_required')->label('Approval')->formatStateUsing(static fn (mixed $state): string => (bool) $state ? 'Required' : 'Optional'),
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
        return ['index' => ListAutomationRecipes::route('/'), 'create' => CreateAutomationRecipe::route('/create'), 'edit' => EditAutomationRecipe::route('/{record}/edit')];
    }
}
