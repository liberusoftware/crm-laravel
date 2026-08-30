<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadCaptureFilament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Liberu\CRM\LeadCapture\Models\CapturedLead;

final class CapturedLeadResource extends Resource
{
    protected static ?string $model = CapturedLead::class;

    protected static ?string $navigationLabel = 'Lead Inbox';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([]);
    }

    public static function getPages(): array
    {
        return [];
    }
}
