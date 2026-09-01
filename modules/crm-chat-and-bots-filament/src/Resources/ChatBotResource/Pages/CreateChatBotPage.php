<?php

declare(strict_types=1);

namespace Liberu\CRM\ChatAndBotsFilament\Resources\ChatBotResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\ChatAndBots\Actions\CreateChatBot;
use Liberu\CRM\ChatAndBotsFilament\Resources\ChatBotResource;

final class CreateChatBotPage extends CreateRecord
{
    protected static string $resource = ChatBotResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();

        return app(CreateChatBot::class)->execute((int) $user?->getAttribute('current_team_id'), (int) $user?->getKey(), (string) $data['name'], (array) $data['playbook'], (array) ($data['channels'] ?? []));
    }
}
