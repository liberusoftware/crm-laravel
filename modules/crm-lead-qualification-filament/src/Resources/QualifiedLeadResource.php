<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualificationFilament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Liberu\CRM\LeadQualification\Models\QualifiedLead;

final class QualifiedLeadResource extends Resource
{
    protected static ?string $model = QualifiedLead::class;

    protected static ?string $navigationLabel = 'Lead Qualification';

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
