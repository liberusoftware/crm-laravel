# CRM documentation conformance record

This document records the conformance map requested by [crm-laravel issue
#707](https://github.com/liberusoftware/crm-laravel/issues/707). It is a
planning and verification record; it does not change application behavior.

## Decisions

- The CRM domain catalog in the Liberu documentation repository is the
  capability source of truth.
- Each CRM capability owns a domain module under `modules/crm-*`.
- API, Filament, and Livewire concerns are separate presentation modules when
  a capability exposes that surface.
- Foundation services remain independently installable `liberusoftware/*`
  packages and are declared by the root `composer.json`.
- Tenant context, authorization, validation, persistence, and lifecycle rules
  belong to the owning module. The root `app/` layer composes modules and
  contains host-only concerns.
- Issue #707 itself is documentation-only. Code changes are tracked against
  the capability issues and security issues that follow this map.

## Capability coverage

The 95 architecture issues numbered #612–#706 have a one-to-one mapping to
the following domain modules. Each row has a corresponding module feature
test under `tests/Feature`.

| Issue range | Verification source |
| --- | --- |
| #612–#623 | Account-based marketing, account planning, activities, advertising, advocacy, affiliate management, agency workspace, AI reception, attribution, business process management, campaigns, and case management modules |
| #624–#635 | Channel gateway, channel sales, chat and bots, client onboarding, collaboration, communities, consent and preferences, contact center, contracts, conversation analytics, conversation intelligence, and conversion optimization modules |
| #636–#647 | CPQ, CRM analytics, CRM core, CRM copilot, CRM documents, CRM search, customer data model, customer data platform, customer self-service, customer success, data operations, and deal registration modules |
| #648–#659 | Dialer and outreach, email marketing, email productivity, enrichment, events and webinars, feedback and voice of customer, field service coordination, forecasting, forms and surveys, goals and performance, journey orchestration, and knowledge modules |
| #660–#671 | Journey orchestration, knowledge, landing pages and funnels, lead capture, lead qualification, learning and courses, loyalty, marketing agent, marketing development funds, marketing resources, memberships, and mobile messaging modules |
| #672–#683 | Omnichannel service, orders and payments workspace, partner relationship management, personalization, playbooks and enablement, predictive models, product workspace, projects, proposals and quotes, prospecting, prospecting agent, and quotas and incentives modules |
| #684–#695 | Reputation management, resource planning, revenue intelligence, revenue lifecycle, routing, SaaS packaging, sales engagement, sales pipelines, sales workspace, sandbox and release management, scheduling, and segmentation modules |
| #696–#706 | Service agent, service analytics, SLA and entitlements, telephony, templates and snapshots, territories and ownership, unified conversations, usage wallet and rebilling, web intent, white label, and work management modules |

The range table is intentionally paired with executable checks rather than
serving as a second package catalog. The authoritative checks are:

```text
find modules -mindepth 1 -maxdepth 1 -type d -name 'crm-*'
find tests/Feature -type f -iname '*ModuleTest.php'
php artisan module:validate
php artisan foundation:doctor
```

The current tree contains 95 domain modules and 95 capability feature tests.
Each domain module has its own manifest and provider; presentation modules are
discovered independently through the module manager.

## Foundation and Composer boundary

The root manifest includes every `liberusoftware/*` requirement present in the
boilerplate foundation manifest. Composer is the dependency authority for
foundation package versions and the lockfile records the resolved graph.

Foundation package coverage includes application, module management, identity,
two-factor authentication, sessions and devices, profiles, organizations and
teams, roles and permissions, localization, currency context, notifications,
files and media, search, audit, feature flags, API access, webhooks,
integrations, analytics, import/export, activity/comments, settings, scheduler
and queues, observability, developer experience, and theme support.

## Security coverage

The team invitation authorization boundary rejects privileged roles in both
the invitation creation and acceptance paths, normalizes role and email
values, and binds acceptance to the authenticated user's email. The regression
coverage is in `tests/Feature/ModuleIntegrationCoverageTest.php`.

`SECURITY.md` defines private vulnerability reporting for
[issue #708](https://github.com/liberusoftware/crm-laravel/issues/708). GitHub
repository security-advisory settings remain an administrator-controlled
repository setting and are not represented as application code.

## Ordered verification plan

1. Resolve the Composer graph and confirm every foundation package is present.
2. Validate module manifests and dependency ordering.
3. Run focused capability and authorization tests after each module change.
4. Run the complete Laravel test suite against the supported SQLite test
   configuration.
5. Run Pint, PHPStan, Composer validation, and the whitespace check.
6. Compare the issue catalog, module catalog, and feature-test catalog before
   merging to `main` or creating a release.

The plan is complete for the current issue set. A future capability issue must
add a domain module, its required presentation modules, focused tests, and a
row in this record before it is considered implemented.
