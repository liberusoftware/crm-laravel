# Lead intake

Zernio CRM supports public lead forms and authenticated CSV imports. Both paths create or update the team contact and lead records used by the pipeline, and imported enquiries can also open a conversation in the Unified Helpdesk.

## CSV import

Download the current template with an authenticated request:

```http
GET /api/v1/leads/import/template
```

Upload a UTF-8 CSV as `file`:

```http
POST /api/v1/leads/import
Content-Type: multipart/form-data
```

Required columns are `name` and either `email` or `phone`. Optional columns are `last_name`, `company`, `source`, `message`, `external_key`, `pipeline_id`, and `stage_id`. Common aliases such as `full_name`, `email_address`, `mobile`, `organisation`, `notes`, and `external_id` are accepted.

Imports are limited to 5,000 rows and 10 MB. The signed-in user must be the team owner or have an administrator, manager, marketing, or sales role. Email matches use the existing encrypted-contact blind index. An `external_key` makes repeat imports idempotent; when it is omitted, the normalized email or phone is used.

When both `pipeline_id` and `stage_id` are provided, the IDs must belong to the current team and a deal is created or updated. A non-empty `message` creates an inbound `lead-import` conversation for the helpdesk. Row-level validation failures are returned in the `errors` array while valid rows continue processing.

Public forms remain available at `POST /forms/{leadForm}/submit`, are rate limited, and resolve their team from the form, landing page, or campaign before writing contacts and leads. This prevents a form from accidentally creating records outside its owning team.
