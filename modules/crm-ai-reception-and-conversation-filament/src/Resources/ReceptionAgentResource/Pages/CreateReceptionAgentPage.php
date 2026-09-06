<?php

declare(strict_types=1);

namespace Liberu\CRM\AIReceptionAndConversationFilament\Resources\ReceptionAgentResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\AIReceptionAndConversation\Actions\CreateReceptionAgent;
use Liberu\CRM\AIReceptionAndConversationFilament\Resources\ReceptionAgentResource;

final class CreateReceptionAgentPage extends CreateRecord
{
    protected static string $resource = ReceptionAgentResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();

        return app(CreateReceptionAgent::class)->execute((int) $user?->getAttribute('current_team_id'), (int) $user?->getKey(), $data);
    }
}
