<?php

declare(strict_types=1);

namespace Liberu\CRM\TemplatesAndSnapshots\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\TemplatesAndSnapshots\Actions\InstallSnapshot;
use Liberu\CRM\TemplatesAndSnapshots\Actions\RollbackSnapshot;
use Liberu\CRM\TemplatesAndSnapshots\Actions\UpdateSnapshot;
use Liberu\CRM\TemplatesAndSnapshots\Queries\SnapshotQuery;
use Livewire\Component;

final class SnapshotDashboard extends Component
{
    public function updateSnapshot(int $snapshotId, string $name, array $payload, string $status, UpdateSnapshot $action): void
    {
        validator(compact('name', 'payload', 'status'), [
            'name' => ['required', 'string', 'max:255'],
            'payload' => ['required', 'array'],
            'status' => ['required', 'in:draft,published'],
        ])->validate();

        $this->teamAction($snapshotId, fn (int $teamId, int $actorId): mixed => $action->execute($teamId, $actorId, $snapshotId, compact('name', 'payload', 'status')));
        $this->dispatch('snapshot-updated');
    }

    public function installSnapshot(int $snapshotId, InstallSnapshot $action): void
    {
        $this->teamAction($snapshotId, fn (int $teamId, int $actorId): mixed => $action->execute($teamId, $actorId, $snapshotId));
        $this->dispatch('snapshot-installed');
    }

    public function rollbackSnapshot(int $snapshotId, int $version, RollbackSnapshot $action): void
    {
        abort_unless($version > 0, 422, 'A valid snapshot version is required.');
        $this->teamAction($snapshotId, fn (int $teamId, int $actorId): mixed => $action->execute($teamId, $actorId, $snapshotId, $version));
        $this->dispatch('snapshot-rolled-back');
    }

    public function render(SnapshotQuery $q): View
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return app('view')->make('crm-templates-and-snapshots-livewire::dashboard', ['snapshots' => $q->list((int) $id)]);
    }

    private function teamAction(int $snapshotId, callable $action): void
    {
        abort_unless($snapshotId > 0, 404);
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null && $user->getAuthIdentifier() !== null, 403);
        $action((int) $user->current_team_id, (int) $user->getAuthIdentifier());
    }
}
