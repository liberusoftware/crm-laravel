# Marketing setup and operating guide

## Platform administration

Run `php artisan migrate` before enabling the new screens. A super admin's first panel login opens **Platform setup** until setup is saved. Existing sessions and workspace pages remain accessible. Return to this page in the admin panel to change providers or rotate credentials.

Choose **Standalone platform APIs** or **Zernio**. This choice governs new social publishing and campaign management across workspaces; ordinary users and workspace administrators cannot change it. Previously scheduled posts retain the provider selected when they were scheduled. Managed campaigns retain their original provider.

Platform secrets are stored in an encrypted settings record, excluded from serialization, and never hydrated into either wizard. Blank credential inputs retain the existing value. Keep `APP_KEY` backed up and do not rotate it without migrating encrypted secrets.

Configure the relevant application IDs, application secrets, supported Meta Graph and LinkedIn API versions, and optional AI service credentials. With Zernio, enter its API key and connect each workspace's accounts through its own profile. Platform-specific capabilities and approvals still apply; Zernio does not remove the social networks' own restrictions.

Configure workspace OAuth callback URLs as `/social-connections/oauth/{service}/callback` in the provider applications. These are separate from the `/oauth/{provider}/callback` login callbacks. The application ID and secret belong to the platform operator; workspace users authorize their own accounts using OAuth consent. Application credentials configured in the wizard override environment credentials for these connections.

## Workspace onboarding

New workspace owners are directed to **Workspace setup** after panel login. Owners and workspace managers configure account identity, workspace name, timezone, brand name, website, audience, tone, preferred channels, and content approval. Members without management access cannot change workspace-wide setup. Changing an account email clears its email verification.

After setup, open **Marketing connections**. With Zernio, each workspace gets a separate profile and a hosted account connection flow. With standalone APIs, authorize a supported account and set its publishing destination: Facebook Page ID, Instagram professional account ID, or LinkedIn author URN. X publishes to the authorized account. Tokens are encrypted, and publishing explicitly filters by workspace. Keep one active standalone connection per channel; disable unused connections from this page. Ambiguous standalone connections fail explicitly. Disabling publishing does not revoke the grant at the external provider.

For advertising, connect an advertising account in the existing account resource. Choose the network and platform ad account ID in the account form. For Zernio, select the connection from the workspace-scoped dropdown; its ID is stored as `metadata.zernio_account_id`. Meta direct accounts require a token authorized for that ad account. `metadata.currency_exponent` defaults to 2; set the correct precision for non-two-decimal currencies before syncing a budget.

## Content and publishing

Use **Social media posts** to write content or request an AI draft. Drafts support links and public image/video URLs according to the selected provider. Use **Content calendar** for the monthly schedule in the workspace timezone. The API uses ISO 8601 timestamps; include an explicit offset.

For Zernio posts, choose explicit connected accounts in the composer. Channel-only selections are available for planning drafts but cannot be scheduled until connected-account targets are selected. The API accepts targets such as `{"platform":"facebook","account_id":"connected-account-id"}`; foreign profile accounts are rejected.

Approve content and then use **Schedule post**. When review is enabled, scheduling requires a workspace manager's approval. Content, media, links, or target changes invalidate approval. AI never publishes a post or activates a paid campaign.

The scheduler claims each due post atomically. Successful per-platform delivery IDs are retained. Zernio responses remain **Publishing** until publication is confirmed, and the scheduler polls known remote posts for terminal status. Timeouts and uncertain failures become **Needs reconciliation**. They do not trigger automatic retries or a switch to another provider. Workspace managers can record a verified delivery resolution, including provider evidence, through **Reconcile delivery**.

Run Laravel's scheduler every minute:

```sh
php artisan schedule:run
```

Set `APP_URL` to the public HTTPS application URL and run `php artisan storage:link` so uploaded images are reachable by the publishing providers. Keep the application clock in UTC; workspace timezones control calendar display and date entry. Explicit API timestamp offsets are normalized before persistence.

Use a shared persistent cache when running more than one application instance. The scheduled publishing command also uses overlap protection.

### Supported publishing paths

| Path | Supported by this composer |
| --- | --- |
| Zernio | Connected Zernio publishing channels; text, images and video subject to network requirements |
| Direct Meta | Facebook text/link/image posts and Instagram image posts |
| Direct X | Text/link posts through API v2 |
| Direct LinkedIn | Text/link posts through the Posts API with an explicitly configured supported version |

Native video uploads and native X/LinkedIn image registration are not implemented by this composer. These selections fail explicitly rather than discarding media. Use Zernio for those media workflows. The older standalone service classes are not used by the new scheduler.

## Advertising lifecycle

Campaigns support provider creation, settings synchronization, explicit activation, pausing, archival, and analytics through the management API. Creation starts paused. Campaign copy, headlines and calls to action can be generated as reviewable drafts in the campaign form. An empty campaign still needs platform-valid ad groups and ads before it can deliver.

Zernio campaign operations verify both the workspace connection and the campaign's visibility within that profile and ad account. Standalone campaign management supports Meta, verifies remote ad-account ownership before updates, and converts whole-unit budgets to the configured currency precision. Unsupported native ad networks return an explicit error; use Zernio for unified campaign operations. Network-specific unsupported operations are surfaced as errors.

Creation timeouts retain a pending marker, preventing accidental duplicate campaigns. Reconcile the campaign at the provider before changing that marker. Campaign activation is a separate action because it may incur advertising charges.

These changes manage campaign lifecycle and draft copy. They do not add automatic creation of every network's ad groups, targeting schema, creatives, or conversion tracking. Existing resources remain available for those records; complete provider-specific creative setup in the relevant ad manager before activation.

## AI content service contract

The super admin may enable an HTTPS content-generation service. This is a configurable content gateway, not an assumed Zernio AI endpoint. The CRM sends a bearer-authenticated JSON request:

```json
{
  "purpose": "post",
  "brief": "Introduce our new service",
  "platform": "linkedin",
  "brand": {"brand_name": "Example", "tone": "professional"},
  "require_human_review": true
}
```

Return JSON containing required `content` and optional `headline` and `call_to_action` strings. `purpose` is `post` or `advertisement`. Failures preserve the existing draft. Credentials and service URLs remain under super-admin control.

## Authenticated management API

All endpoints are under `/api/v1/marketing` and require a current workspace and appropriate permissions:

- `GET /posts`: paginated workspace posts.
- `POST /posts`: create a draft; provider, team and approval overrides are ignored.
- `PUT /posts/{post}`: edit a draft or scheduled post, returning it to draft.
- `POST /posts/{post}/approve`: manager approval.
- `POST /posts/{post}/schedule`: schedule with `scheduled_at`.
- `POST /posts/{post}/cancel`: cancel a local draft or scheduled post before dispatch.
- `POST /generate`: rate-limited generation with `purpose`, `brief`, and `platform`.
- `POST /campaigns/{campaign}/manage`: `operation` is `create`, `update`, `activate`, `pause`, `archive`, or `analytics`.

Provider contracts were checked against the [Zernio campaign reference](https://docs.zernio.com/ad-campaigns/create-ad-campaign) and its [campaign update reference](https://docs.zernio.com/ad-campaigns/update-ad-campaign). Live provider calls require valid credentials and approved application permissions; automated tests use HTTP fakes.
