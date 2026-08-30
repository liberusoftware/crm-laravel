<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadCapture\Filament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\CRM\LeadCapture\Filament\Resources\LeadCaptureResource\Pages\CreateLeadCapture;
use Liberu\CRM\LeadCapture\Filament\Resources\LeadCaptureResource\Pages\ListLeadCaptures;
use Liberu\CRM\LeadCapture\Models\LeadCapture;

final class LeadCaptureResource extends Resource
{
    protected static ?string $model = LeadCapture::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-inbox';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([Select::make('kind')->options(['leads_inbox' => 'Leads inbox', 'manual' => 'Manual', 'import' => 'Import', 'api' => 'API', 'form' => 'Form', 'survey' => 'Survey', 'qr_code' => 'QR code', 'chat' => 'Chat', 'call' => 'Call', 'advertisement' => 'Advertisement', 'event' => 'Event', 'referral' => 'Referral'])->required(), TextInput::make('name')->maxLength(255), TextInput::make('email')->email(), TextInput::make('phone')->maxLength(80), TextInput::make('source')->maxLength(120), TextInput::make('source_medium')->maxLength(120), TextInput::make('source_campaign')->maxLength(160), Textarea::make('failure_reason')->columnSpanFull(), Select::make('status')->options(['received' => 'Received', 'processing' => 'Processing', 'converted' => 'Converted', 'rejected' => 'Rejected', 'failed' => 'Failed'])->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('kind')->badge(), TextColumn::make('name')->searchable(), TextColumn::make('email')->searchable(), TextColumn::make('source')->badge(), TextColumn::make('status')->badge(), TextColumn::make('captured_at')->dateTime()]);
    }

    public static function getPages(): array
    {
        return ['index' => ListLeadCaptures::route('/'), 'create' => CreateLeadCapture::route('/create')];
    }
}
