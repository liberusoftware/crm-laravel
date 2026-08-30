<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadCapture\Filament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\CRM\LeadCapture\Filament\Resources\CaptureReferralResource\Pages\CreateCaptureReferral;
use Liberu\CRM\LeadCapture\Filament\Resources\CaptureReferralResource\Pages\ListCaptureReferrals;
use Liberu\CRM\LeadCapture\Models\CaptureReferral;

final class CaptureReferralResource extends Resource
{
    protected static ?string $model = CaptureReferral::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-share';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('code')->required(), TextInput::make('referrer_type'), TextInput::make('referrer_id')->numeric(), TextInput::make('referred_type'), TextInput::make('referred_id')->numeric(), Select::make('status')->options(['pending' => 'Pending', 'qualified' => 'Qualified', 'converted' => 'Converted', 'rejected' => 'Rejected'])->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('code')->searchable(), TextColumn::make('referrer_type'), TextColumn::make('referrer_id'), TextColumn::make('referred_type'), TextColumn::make('status')->badge()]);
    }

    public static function getPages(): array
    {
        return ['index' => ListCaptureReferrals::route('/'), 'create' => CreateCaptureReferral::route('/create')];
    }
}
