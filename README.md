# Liberu CRM

[![Tests](https://github.com/liberusoftware/crm-laravel/actions/workflows/tests.yml/badge.svg)](https://github.com/liberusoftware/crm-laravel/actions/workflows/tests.yml)
[![Install](https://github.com/liberusoftware/crm-laravel/actions/workflows/install.yml/badge.svg)](https://github.com/liberusoftware/crm-laravel/actions/workflows/install.yml)
[![Docker](https://github.com/liberusoftware/crm-laravel/actions/workflows/main.yml/badge.svg)](https://github.com/liberusoftware/crm-laravel/actions/workflows/main.yml)
[![Coverage](https://codecov.io/gh/liberusoftware/crm-laravel/branch/main/graph/badge.svg)](https://codecov.io/gh/liberusoftware/crm-laravel)
[![Latest release](https://img.shields.io/github/v/release/liberusoftware/crm-laravel)](https://github.com/liberusoftware/crm-laravel/releases)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/license/mit/)

Liberu CRM is an open-source, multi-tenant customer relationship management application for managing contacts, companies, leads, opportunities, deals, tasks, communications, campaigns, and support work in one place.

It is built for teams that need control over their data and integrations. The application can be self-hosted, extended with Laravel, and operated with the included Docker and Kubernetes configuration.

## What it includes

- Contact, company, lead, opportunity, deal, task, note, document, and activity management.
- Sales pipelines, stages, forecasting, lead scoring, reporting, dashboards, and exports.
- Email templates and tracking, marketing campaigns, social publishing, forms, landing pages, and workflow automation.
- Unified help desk features for tickets, messages, live chat, knowledge-base articles, and chatbot interactions.
- Team membership, per-team roles and permissions, tenancy, audit logs, SSO/SAML, and OAuth connections.
- Advertising account management for Google, Facebook, LinkedIn, and Instagram.
- Communication integrations including Twilio, WhatsApp Business, Mailchimp, Gmail, Outlook, IMAP, POP3, calendars, Stripe, and YouTube.
- A versioned Sanctum-protected REST API, a Filament administration panel, and Livewire-driven application screens.

## Technology

- PHP 8.5+
- Laravel 13
- Filament 5
- Livewire 4
- Laravel Jetstream and Socialstream
- Laravel Sanctum, Octane, Horizon, and Reverb
- MySQL 8+, PostgreSQL, or SQLite
- Redis 7 for queues, cache, and real-time workloads

## Installation

### Requirements

Install PHP 8.5 or newer with Composer, Node.js and npm, and one of the supported databases. Redis is required for queue, cache, or real-time features that use it.

### Automated setup

The interactive installer configures a local application, installs dependencies, builds assets, migrates and seeds the database, and runs the test suite:

```bash
git clone https://github.com/liberusoftware/crm-laravel.git
cd crm-laravel
./setup.sh
```

### Manual setup

```bash
git clone https://github.com/liberusoftware/crm-laravel.git
cd crm-laravel
composer install
cp .env.example .env
php artisan key:generate
```

Configure the database and application services in `.env`, then run:

```bash
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

The application is available at `http://localhost:8000`. Use `php artisan storage:link` when serving uploaded files locally.

### Docker Compose

The repository includes a PHP 8.5 application image and services for MySQL and Redis:

```bash
cp .env.example .env
docker compose up -d --build
docker compose exec app php artisan migrate --seed
```

Optional Compose profiles provide Horizon, Reverb, a queue worker, and Mailpit:

```bash
docker compose --profile horizon --profile reverb --profile worker --profile mail up -d
```

Kubernetes manifests and validation scripts are available under [`k8s/`](k8s/). Review secrets and storage settings before deploying to a shared or production cluster.

## Configuration

Start with [`.env.example`](.env.example). Keep application keys, OAuth secrets, webhook secrets, and service credentials outside the repository. Configure only the integrations required by your deployment.

The application supports team-aware settings and encrypted credentials for integrations. Queue workers, scheduled tasks, Horizon, Reverb, and tenancy should be configured according to the deployment environment rather than enabled blindly in development.

## API

The REST API is versioned under `/api/v1` and uses Laravel Sanctum authentication. The endpoint reference and authentication examples are in [`docs/api-documentation.md`](docs/api-documentation.md).

## Documentation

- [`docs/IMPLEMENTATION_SUMMARY.md`](docs/IMPLEMENTATION_SUMMARY.md) — application capabilities and implementation status.
- [`docs/MODULAR_ARCHITECTURE.md`](docs/MODULAR_ARCHITECTURE.md) — module boundaries and extension points.
- [`docs/UNIFIED_HELPDESK.md`](docs/UNIFIED_HELPDESK.md) — help desk and communication workflows.
- [`docs/contact-management-ui-ux.md`](docs/contact-management-ui-ux.md) — contact management interface.
- [`docs/email-tracking.md`](docs/email-tracking.md) — email tracking configuration and behavior.
- [`docs/oauth-authentication.md`](docs/oauth-authentication.md) — OAuth connection setup.
- [`docs/workflow-automation.md`](docs/workflow-automation.md) — automation triggers and actions.

## Development

The default development branch is `main`. Run the relevant checks before opening a pull request:

```bash
php artisan test
./vendor/bin/pint
./vendor/bin/phpstan analyse
npm run build
```

Use focused tests while iterating:

```bash
php artisan test --filter=ContactTest
```

### Coverage

The `tests` workflow runs the complete PHPUnit suite with Xdebug, writes `coverage.xml`, and uploads the report to [Codecov](https://codecov.io/gh/liberusoftware/crm-laravel). Generate the same report locally with:

```bash
php artisan test --coverage-clover=coverage.xml
```

Keep business logic in actions and services, enforce access through policies, and preserve tenant and team boundaries when adding models, routes, jobs, or Filament resources.

## Contributing

1. Create a branch from `main`.
2. Make a focused change with accompanying tests and documentation where behavior changes.
3. Run the applicable PHP and frontend checks.
4. Open a pull request against `main` with the user impact, verification commands, and any migration or configuration requirements.

Please report security vulnerabilities privately through the repository maintainers rather than opening a public issue.

## License

Liberu CRM is released under the [MIT License](https://opensource.org/license/mit/).

## Liberu Software

Liberu CRM is maintained by [Liberu Software](https://www.liberu.co.uk). Visit the [Liberu website](https://www.liberu.co.uk) for product and support information.


### Automatic module and theme publishing

`.github/workflows/sync-packages.yml` publishes packages changed under `modules/`
or `themes/` on pushes to `main`. A manual run reconciles all tracked packages.
Each package needs a `composer.json` with a `liberusoftware/*` name. Files are
copied from Git into the root of `liberusoftware/module-<directory>` or
`liberusoftware/theme-<directory>`. Where `.liberu-meta.json` defines a
`repositoryPrefix`, that design is authoritative (for example,
`modules/module-manager` becomes `module-module-manager`). Without a configured
prefix, an existing `module-` or `theme-` directory prefix is kept. Destination
history is preserved, deleted files are removed, and unchanged packages do not
receive another commit or release. Removing an entire package leaves its public
repository intact. This workflow makes tracked package edits publishable; install
checks still require the project's Composer dependencies to resolve to those
published package contents.

Configure these secrets in the `packagist` GitHub environment (or as repository
or organization secrets available to this repository):

- `MODULE_SYNC_TOKEN`: organization-authorized GitHub token able to create public
  repositories and read/write package contents and releases across the target
  repositories. Because package snapshots include `.github/workflows`, allow
  workflow writes too. A classic PAT needs `repo` and `workflow` scopes and an
  organization role permitted to create repositories. The default `GITHUB_TOKEN`
  cannot publish across these repositories.
- `PACKAGIST_USERNAME`: Packagist account that owns or maintains these packages.
- `PACKAGIST_API_TOKEN`: that account's **main** API token, required to submit
  new packages as well as update existing packages.

New packages start at their declared stable Composer version, or `v1.0.0`.
Existing packages use the highest stable repository tag: a higher declared
Composer version wins, `BREAKING CHANGE:` or `type!:` commits bump major,
`feat:` commits bump minor, and other changes bump patch. Only commits touching
the package since the last successful sync affect this decision. The first
automated run and manual runs reconcile all packages, so canceled or failed
runs cannot silently drop package changes. Tags and commits are
pushed atomically, then a GitHub release is created and the package is submitted
to or refreshed on Packagist. Rerunning a failed job completes publication
without issuing another release for the same contents. Concurrent pushes from
other projects are rejected instead of force-pushed; rerun the failed job.

The source project and its lockfile are not rewritten by publication. Different
projects publishing the same directory name share a destination repository;
coordinate their package contents. Run the publisher's local tests with
`python3 -m unittest discover -s .github/scripts -p 'test_sync_packages.py'`.
