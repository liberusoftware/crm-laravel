<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Contact;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\Pipeline;
use App\Models\Stage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Liberu\CRM\UnifiedConversations\Actions\SyncExternalConversation;
use RuntimeException;

final class LeadImportService
{
    /** @return array{created:int,updated:int,skipped:int,errors:list<array{row:int,error:string}>} */
    public function import(UploadedFile $file, int $teamId, int $actorId): array
    {
        $handle = fopen($file->getRealPath(), 'rb');
        if ($handle === false) {
            throw new RuntimeException('The uploaded file could not be read.');
        }

        try {
            $header = fgetcsv($handle);
            if (! is_array($header)) {
                throw new RuntimeException('The CSV file is empty.');
            }
            $columns = $this->columns($header);
            if (! isset($columns['name']) || (! isset($columns['email']) && ! isset($columns['phone']))) {
                throw new RuntimeException('CSV headers must include name and email or phone.');
            }

            $summary = ['created' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => []];
            $rowNumber = 1;
            while (($row = fgetcsv($handle)) !== false) {
                $rowNumber++;
                if ($row === [null] || collect($row)->filter(fn ($value): bool => trim((string) $value) !== '')->isEmpty()) {
                    continue;
                }
                if ($rowNumber > 5001) {
                    $summary['errors'][] = ['row' => $rowNumber, 'error' => 'Import limit is 5,000 rows.'];
                    break;
                }

                try {
                    $result = $this->importRow($this->row($row, $columns), $teamId, $actorId);
                    $summary[$result]++;
                } catch (RuntimeException $exception) {
                    $summary['skipped']++;
                    $summary['errors'][] = ['row' => $rowNumber, 'error' => $exception->getMessage()];
                }
            }

            return $summary;
        } finally {
            fclose($handle);
        }
    }

    /** @param list<string|null> $header @return array<string, int> */
    private function columns(array $header): array
    {
        $aliases = [
            'name' => ['name', 'full_name', 'fullname', 'first_name'],
            'last_name' => ['last_name', 'surname'],
            'email' => ['email', 'email_address', 'work_email'],
            'phone' => ['phone', 'phone_number', 'mobile', 'telephone'],
            'company' => ['company', 'company_name', 'organisation', 'organization'],
            'source' => ['source', 'lead_source', 'campaign'],
            'message' => ['message', 'notes', 'enquiry', 'inquiry'],
            'external_key' => ['external_key', 'external_id', 'lead_id', 'id'],
            'pipeline_id' => ['pipeline_id', 'pipeline'],
            'stage_id' => ['stage_id', 'stage'],
        ];
        $columns = [];
        foreach ($header as $index => $value) {
            $normalized = Str::snake(trim((string) $value));
            foreach ($aliases as $field => $names) {
                if (in_array($normalized, $names, true) && ! isset($columns[$field])) {
                    $columns[$field] = $index;
                }
            }
        }

        return $columns;
    }

    /** @param list<string|null> $row @param array<string, int> $columns @return array<string, string> */
    private function row(array $row, array $columns): array
    {
        $data = [];
        foreach ($columns as $field => $index) {
            $data[$field] = trim((string) ($row[$index] ?? ''));
        }

        return $data;
    }

    /** @param array<string, string> $data */
    private function importRow(array $data, int $teamId, int $actorId): string
    {
        $name = trim($data['name'] ?? '');
        $email = strtolower(trim($data['email'] ?? ''));
        $phone = trim($data['phone'] ?? '');
        if ($name === '' || ($email === '' && $phone === '')) {
            throw new RuntimeException('Name and email or phone are required.');
        }
        if ($email !== '' && ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Email address is invalid.');
        }
        $source = trim($data['source'] ?? '');
        $company = trim($data['company'] ?? '');
        $message = trim($data['message'] ?? '');
        $externalKey = trim($data['external_key'] ?? '');

        return DB::transaction(function () use ($data, $teamId, $actorId, $name, $email, $phone, $source, $company, $message, $externalKey): string {
            $contact = $email !== ''
                ? Contact::withoutGlobalScopes()->where('team_id', $teamId)->where('email_hash', Contact::hashEmail($email))->first()
                : null;
            $wasExisting = $contact !== null;
            $contact ??= new Contact();
            $contact->fill([
                'team_id' => $teamId,
                'name' => $name,
                'last_name' => $data['last_name'] ?? '',
                'email' => $email !== '' ? $email : null,
                'phone_number' => $phone !== '' ? $phone : null,
                'source' => $source !== '' ? $source : 'import',
                'status' => 'active',
                'lifecycle_stage' => 'lead',
                'metadata' => ['imported_by' => $actorId, 'imported_at' => now()->toIso8601String(), 'company' => $company !== '' ? $company : null],
            ])->save();

            $resolvedExternalKey = $externalKey !== '' ? $externalKey : hash('sha256', $email !== '' ? $email : $phone);
            $lead = Lead::withoutGlobalScopes()->where('team_id', $teamId)->where('import_key', $resolvedExternalKey)->first() ?? new Lead();
            $leadWasExisting = $lead->exists;
            $lead->fill(['team_id' => $teamId, 'import_key' => $resolvedExternalKey, 'status' => 'new', 'source' => $source !== '' ? $source : 'import', 'contact_id' => $contact->id, 'user_id' => $actorId, 'lifecycle_stage' => 'lead', 'custom_fields' => ['imported' => true, 'company' => $company !== '' ? $company : null]])->save();
            $lead->calculateScore();

            if (($data['pipeline_id'] ?? '') !== '' || ($data['stage_id'] ?? '') !== '') {
                if (! ctype_digit($data['pipeline_id'] ?? '') || ! ctype_digit($data['stage_id'] ?? '')) {
                    throw new RuntimeException('Pipeline and stage must be numeric IDs when provided.');
                }
                $pipeline = Pipeline::withoutGlobalScopes()->where('team_id', $teamId)->find((int) $data['pipeline_id']);
                $stage = Stage::withoutGlobalScopes()->where('team_id', $teamId)->where('pipeline_id', (int) $data['pipeline_id'])->find((int) $data['stage_id']);
                if ($pipeline === null || $stage === null) {
                    throw new RuntimeException('Pipeline or stage does not belong to this team.');
                }
                Deal::withoutGlobalScopes()->updateOrCreate(['team_id' => $teamId, 'name' => $name, 'contact_id' => $contact->id, 'pipeline_id' => $pipeline->id], ['value' => 0, 'stage' => $stage->name, 'stage_id' => $stage->id, 'user_id' => $actorId, 'probability' => 0]);
            }

            if ($message !== '') {
                app(SyncExternalConversation::class)->execute($teamId, [
                    'channel' => 'lead-import',
                    'external_id' => $resolvedExternalKey,
                    'subject' => 'New lead enquiry from '.$name,
                    'participant' => ['identity' => $email !== '' ? $email : $phone, 'name' => $name],
                    'message' => ['external_id' => $resolvedExternalKey, 'body' => $message, 'direction' => 'inbound'],
                    'metadata' => ['lead_id' => $lead->id, 'contact_id' => $contact->id],
                ]);
            }

            return $wasExisting || $leadWasExisting ? 'updated' : 'created';
        });
    }
}
