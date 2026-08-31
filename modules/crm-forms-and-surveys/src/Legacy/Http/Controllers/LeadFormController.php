<?php

namespace App\Http\Controllers;

use App\Jobs\ExecuteWorkflowAction;
use App\Models\Campaign;
use App\Models\Contact;
use App\Models\Lead;
use App\Models\LeadForm;
use App\Models\Workflow;
use App\Services\LeadScoringService;
use Illuminate\Http\Request;

class LeadFormController extends Controller
{
    public function __construct(protected LeadScoringService $leadScoringService) {}

    public function submit(Request $request, LeadForm $leadForm)
    {
        $validatedData = $request->validate($this->getValidationRules($leadForm));

        $teamId = $leadForm->getAttribute('team_id');
        $landingPage = $leadForm->landingPage()->first();
        $campaign = null;
        if (! is_numeric($teamId)) {
            $teamId = $landingPage?->getAttribute('team_id');
        }
        if (! is_numeric($teamId)) {
            $campaign = Campaign::query()->find($landingPage?->getAttribute('campaign_id'));
            $teamId = $campaign?->getAttribute('team_id');
        }
        abort_unless(is_numeric($teamId), 422, 'The lead form is not attached to a team.');
        $contact = $this->createOrUpdateContact($validatedData, (int) $teamId);

        $lead = Lead::create([
            'status' => 'new',
            'source' => 'landing_page',
            'contact_id' => $contact->id,
            'user_id' => $campaign?->getAttribute('user_id'),
            'team_id' => (int) $teamId,
            'potential_value' => $validatedData['potential_value'] ?? null,
            'expected_close_date' => $validatedData['expected_close_date'] ?? null,
            'lifecycle_stage' => 'lead',
        ]);

        // Score the new lead
        $this->leadScoringService->scoreLeads($lead);

        // Trigger workflow actions
        $this->triggerWorkflow($lead);

        return response()->json(['message' => 'Form submitted successfully', 'lead_id' => $lead->id]);
    }

    private function getValidationRules(LeadForm $leadForm): array
    {
        $rules = [];
        foreach ($leadForm->fields as $field) {
            $raw = $field['validation'] ?? 'required';
            $parts = is_array($raw) ? $raw : explode('|', (string) $raw);
            $filtered = [];
            foreach ($parts as $rule) {
                $name = is_string($rule) ? explode(':', $rule)[0] : '';
                if (! in_array($name, ['regex', 'not_regex'], true)
                    && ! str_contains((string) $rule, '\\')) {
                    $filtered[] = $rule;
                }
            }
            $rules[$field['name']] = array_values($filtered) ?: ['required'];
        }

        return $rules;
    }

    private function createOrUpdateContact(array $data, int $teamId): Contact
    {
        $email = strtolower(trim((string) ($data['email'] ?? '')));
        $contact = Contact::withoutGlobalScopes()->where('team_id', $teamId)->where('email_hash', Contact::hashEmail($email))->first() ?? new Contact();
        $contact->fill(['team_id' => $teamId, 'email' => $email, 'name' => $data['name'] ?? null, 'last_name' => $data['last_name'] ?? null, 'phone_number' => $data['phone_number'] ?? null, 'company_size' => $data['company_size'] ?? null, 'industry' => $data['industry'] ?? null])->save();

        return $contact;
    }

    private function triggerWorkflow(Lead $lead): void
    {
        $workflows = Workflow::whereJsonContains('triggers->type', 'lead_created')->get();

        foreach ($workflows as $workflow) {
            $this->executeWorkflowActions($workflow, $lead);
        }
    }

    private function executeWorkflowActions(Workflow $workflow, Lead $lead): void
    {
        foreach ($workflow->actions as $action) {
            ExecuteWorkflowAction::dispatch($action, $lead);
        }
    }
}
