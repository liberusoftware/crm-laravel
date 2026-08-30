<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\User;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $sortBy = (string) $request->input('sort_by', 'created_at');
        $sortDirection = strtolower((string) $request->input('sort_direction', 'desc'));
        $perPage = min(max((int) $request->input('per_page', 15), 1), 100);

        $query = Contact::query()
            ->byTeam($request->user()?->currentTeam?->id)
            ->when($search !== '', function (Builder $query) use ($search): void {
                $like = '%'.addcslashes($search, '\\%_').'%';

                $query->where(function (Builder $searchQuery) use ($search, $like): void {
                    $searchQuery
                        ->where('name', 'like', $like)
                        ->orWhere('last_name', 'like', $like)
                        ->orWhere('industry', 'like', $like)
                        ->orWhere('lifecycle_stage', 'like', $like)
                        ->orWhere('company_size', 'like', $like)
                        ->orWhere('annual_revenue', 'like', $like)
                        ->orWhere('email_hash', Contact::hashEmail($search))
                        ->orWhereHas('company', fn (Builder $companyQuery): Builder => $companyQuery->where('name', 'like', $like));
                });
            })
            ->when($request->filled('status'), fn (Builder $query): Builder => $query->where('status', $request->string('status')->toString()))
            ->when($request->filled('source'), fn (Builder $query): Builder => $query->where('source', $request->string('source')->toString()))
            ->when($request->filled('lifecycle_stage'), fn (Builder $query): Builder => $query->where('lifecycle_stage', $request->string('lifecycle_stage')->toString()))
            ->when($request->filled('company_id'), fn (Builder $query): Builder => $query->where('company_id', (int) $request->input('company_id')));

        $sortBy = in_array($sortBy, ['created_at', 'name', 'last_name', 'status', 'source', 'lifecycle_stage'], true)
            ? $sortBy
            : 'created_at';
        $sortDirection = in_array($sortDirection, ['asc', 'desc'], true) ? $sortDirection : 'desc';

        return $query->orderBy($sortBy, $sortDirection)->paginate($perPage);
    }

    public function store(Request $request)
    {
        $teamId = $request->user()?->currentTeam?->id;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', $this->uniqueEmailRule($teamId)],
            'phone_number' => 'nullable|string|max:20',
            'last_name' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:255',
            'lifecycle_stage' => 'nullable|string|max:255',
            'company_id' => ['nullable', 'integer', Rule::exists('companies', 'id')->where('team_id', $teamId)],
        ]);

        $validated['team_id'] = $teamId;
        $contact = Contact::create($validated);

        return response()->json($contact, 201);
    }

    public function show(Request $request, Contact $contact): Contact
    {
        abort_unless($contact->belongsToTeam($request->user()?->currentTeam?->id), 403);

        return $contact;
    }

    public function update(Request $request, Contact $contact)
    {
        $teamId = $request->user()?->currentTeam?->id;
        abort_unless($contact->belongsToTeam($teamId), 403);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'email' => ['email', $this->uniqueEmailRule($teamId, $contact->id)],
            'phone_number' => 'nullable|string|max:20',
            'last_name' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:255',
            'lifecycle_stage' => 'nullable|string|max:255',
            'company_id' => ['nullable', 'integer', Rule::exists('companies', 'id')->where('team_id', $teamId)],
        ]);

        $contact->update($validated);

        return response()->json($contact, 200);
    }

    private function uniqueEmailRule(?int $teamId, ?int $ignoreId = null): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) use ($teamId, $ignoreId): void {
            $query = Contact::withoutGlobalScopes()
                ->where('team_id', $teamId)
                ->where('email_hash', Contact::hashEmail((string) $value));

            if ($ignoreId !== null) {
                $query->whereKeyNot($ignoreId);
            }

            if ($query->exists()) {
                $fail('The :attribute has already been taken by a contact in this team.');
            }
        };
    }

    public function destroy(Request $request, Contact $contact)
    {
        abort_unless($contact->belongsToTeam($request->user()?->currentTeam?->id), 403);

        $contact->delete();

        return response()->json(null, 204);
    }

    /**
     * Bulk update contacts.
     *
     * Expects: { "ids": [1,2,3], "data": { "status": "active", ... } }
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:contacts,id',
            'data' => 'required|array',
            'data.status' => 'sometimes|string|max:255',
            'data.lifecycle_stage' => 'sometimes|string|max:255',
            'data.source' => 'sometimes|string|max:255',
        ]);

        $allowedFields = ['status', 'lifecycle_stage', 'source', 'industry'];
        $updateData = array_intersect_key($request->input('data'), array_flip($allowedFields));

        if ($updateData === []) {
            return response()->json(['message' => 'No valid fields to update.'], 422);
        }

        $query = Contact::whereIn('id', $request->input('ids'));
        $query->byTeam($request->user()?->currentTeam?->id);
        $count = $query->update($updateData);

        return response()->json(['updated' => $count]);
    }

    /**
     * Bulk delete contacts.
     *
     * Expects: { "ids": [1,2,3] }
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:contacts,id',
        ]);

        $query = Contact::whereIn('id', $request->input('ids'));
        $query->byTeam($request->user()?->currentTeam?->id);
        $count = $query->delete();

        return response()->json(['deleted' => $count]);
    }

    /**
     * Bulk assign contacts to a user.
     *
     * Expects: { "ids": [1,2,3], "user_id": 5 }
     */
    public function bulkAssign(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:contacts,id',
            'user_id' => 'required|integer|exists:users,id',
        ]);

        // Assignee must be a member of the caller's current team, else this
        // leaks records across tenants. Refuse before touching any record.
        $team = $request->user()?->currentTeam;
        $assignee = User::find($request->input('user_id'));
        abort_unless($team && $assignee?->belongsToTeam($team), 403);

        $query = Contact::whereIn('id', $request->input('ids'));
        $query->byTeam($team->id);
        $count = $query->update(['user_id' => $request->input('user_id')]);

        return response()->json(['assigned' => $count]);
    }
}
