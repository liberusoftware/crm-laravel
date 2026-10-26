<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactListController extends Controller
{
    public function index(Request $request, ?string $created_at = null): View
    {
        $teamId = $request->user()?->currentTeam?->getKey();
        abort_unless($teamId, 403);

        $query = Contact::query()->where('team_id', $teamId);

        if ($request->filled('search')) {
            $term = $request->string('search')->toString();
            $query->where(fn ($q) => $q
                ->where('name', 'like', '%'.$term.'%')
                ->orWhere('last_name', 'like', '%'.$term.'%')
                // email is encrypted — exact match via the blind index only.
                // phone_number is encrypted too and not searchable.
                ->orWhere('email_hash', Contact::hashEmail($term))
                ->orWhere('company_size', 'like', '%'.$term.'%')
                ->orWhere('industry', 'like', '%'.$term.'%')
            );
        }

        if ($created_at) {
            try {
                $query->where('created_at', '>=', Carbon::parse($created_at));
            } catch (\Exception) {
                // Invalid date string — skip filter
            }
        }

        $contacts = $query->get();

        return view('contacts.list', ['contacts' => $contacts]);
    }

    public function bulkDelete(Request $request): JsonResponse
    {
        $teamId = $request->user()?->currentTeam?->getKey();
        abort_unless($teamId, 403);

        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:contacts,id',
        ]);

        $count = Contact::whereIn('id', $validated['ids'])
            ->where('team_id', $teamId)
            ->delete();

        return response()->json(['deleted' => $count]);
    }

    public function autocomplete(Request $request): JsonResponse
    {
        $teamId = $request->user()?->currentTeam?->getKey();
        abort_unless($teamId, 403);

        $term = $request->string('query')->toString();

        $contacts = Contact::where(fn ($q) => $q
            ->where('name', 'like', $term.'%')
            // email is encrypted — exact match via the blind index only.
            ->orWhere('email_hash', Contact::hashEmail($term))
        )
            ->where('team_id', $teamId)
            ->limit(10)
            ->get(['id', 'name', 'email']);

        return response()->json($contacts);
    }
}
