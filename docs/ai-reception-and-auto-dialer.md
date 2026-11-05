# Voice reception and outbound sales

Run the database migrations and configure each team's Twilio account SID, encrypted auth token, and default sender in telephony settings. Set `APP_URL` to the public HTTPS application URL so Twilio can reach signed status callbacks. Run Laravel's scheduler every minute (`php artisan schedule:run`). Calls incur charges on the team's Twilio account.

## Inbound reception

Configure a Twilio voice number to POST to `/twilio/ai-reception/{agentId}` for an active **voice** reception agent. Configure the existing Twilio webhook verification token for this route. The receptionist records both caller speech and assistant responses, and uses the configured AI endpoint with a fallback on connection failures.

Set the agent's policy to include:

```json
{"transfer_numbers":{"sales":"+15551234567","support":"+15551234568","human":"+15551234569"}}
```

Callers can press 1 for sales, 2 for support, or 0 for a person. Asking for an agent, human, or operator routes to `human`. Transfers create a handoff audit and preserve the transcript. The configured greeting should explain these options. Transfers require a valid configured destination; these changes do not automatically create support cases or sales opportunities.

## Outbound lists

Authenticated API routes use the caller's current team under `/api/v1/crm/dialer-and-outreach`:

- `POST /lists`: create a list with `name`, `mode`, `compliance`, and `script`.
- `POST /lists/{list}/calls`: enqueue an E.164 `phone`, optional `contact_id`, `consent`, and `next_attempt_at`.
- `PATCH /lists/{list}/state`: set `status` to `active` or `paused`.
- `POST /lists/{list}/dispatch`: place the next eligible call manually.
- `POST /calls/{call}/outcome`: record the agent's disposition.
- `POST /calls/{call}/retry`: requeue a retryable disposition after five minutes, up to three provider attempts.

Example script:

```json
{"opening":"Please hold while I connect you to our sales team.","agent_number":"+15551234567"}
```

Active `power` and `progressive` lists dispatch at most one call per scheduler tick and allow one pending or live call per list. `preview` lists require manual dispatch. A prospect hears the opening and is then connected to the configured agent number. This is sequential dialing; predictive dialing and agent availability tracking are not implemented.

Optional `local_time_policy` uses an explicit timezone, ISO weekdays (Monday 1 through Sunday 7), and a same-day interval with an exclusive end:

```json
{"timezone":"Europe/London","start":"09:00","end":"17:00","days":[1,2,3,4,5]}
```

Consent is checked when enqueuing and dispatching if `compliance.consent_required` is true. Calls queued before this migration have no retained consent and are blocked when consent is required; obtain consent and enqueue them again. A `do_not_call` disposition suppresses that number across the team. Signed callbacks are tied to the individual attempt. Provider completion does not imply a connected sales conversation; agents must record its outcome.

A timeout or malformed provider success leaves the call in `dispatch_unknown`, blocking further calls on that list. Reconcile with Twilio before resolving that call; automatic retries could duplicate a call already accepted by Twilio. Provider credentials and live calls are not exercised by the automated tests.
