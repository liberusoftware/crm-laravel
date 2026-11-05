<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\SocialMediaPostResource\Pages;

use App\Filament\App\Resources\SocialMediaPostResource;
use App\Models\SocialMediaPost;
use App\Services\Marketing\PostWorkflow;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditSocialMediaPost extends EditRecord
{
    protected static string $resource = SocialMediaPostResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['platforms'] = array_map(fn ($target) => is_array($target) ? $target['platform'].'::'.$target['account_id'] : $target, $data['platforms'] ?? []);

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof SocialMediaPost, 404);

        return app(PostWorkflow::class)->update(
            Filament::auth()->user(), Filament::getTenant(), $record, $data,
        );
    }
}
