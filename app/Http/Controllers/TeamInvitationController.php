<?php

namespace App\Http\Controllers;

use App\Actions\Jetstream\InviteTeamMember;
use App\Enums\Role;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Services\TeamManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Laravel\Jetstream\Events\TeamMemberAdded;

class TeamInvitationController extends Controller
{
    public function sendInvitation(Request $request, InviteTeamMember $inviter)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'role' => ['required', 'string'],
            'team_id' => ['required', 'exists:teams,id'],
        ]);

        $team = Team::findOrFail($request->team_id);

        Gate::forUser($request->user())->authorize('addTeamMember', $team);

        $inviter->invite(
            $request->user(),
            $team,
            $request->email,
            $request->role
        );

        return back()->with('success', __('Invitation sent successfully.'));
    }

    public function acceptInvitation(Request $request, int $invitationId)
    {
        $user = $request->user();

        abort_unless($user !== null, 401);

        DB::transaction(function () use ($invitationId, $user): void {
            $invitation = TeamInvitation::query()
                ->lockForUpdate()
                ->findOrFail($invitationId);

            abort_unless(
                strcasecmp((string) $invitation->email, (string) $user->email) === 0,
                403,
                __('You are not authorized to accept this invitation.')
            );

            $role = Role::tryFrom((string) $invitation->role);
            abort_unless($role !== null && in_array($role, TeamManagementService::TEAM_ROLES, true), 403);

            $team = $invitation->team;
            $team->users()->syncWithoutDetaching([
                $user->getKey() => ['role' => $role->value],
            ]);
            TeamMemberAdded::dispatch($team, $user);
            $user->switchTeam($team);
            $invitation->delete();
        });

        return redirect(config('fortify.home'))->with('success', __('You have joined the team!'));
    }
}
