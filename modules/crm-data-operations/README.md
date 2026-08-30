# CRM Data Operations

Independent domain module for team-scoped imports, exports, field mapping, normalization, enrichment orchestration, duplicate detection and merge review, formatting, quality rules, schedules, and exception recovery.

The module owns lifecycle transitions, mapping and normalization primitives, duplicate confidence calculation, exceptions, and audit-ready persistence. Optional API, Filament, and Livewire packages are presentation adapters and delegate mutations to these public actions and services.

Operations accept untrusted source rows and retain only operator-visible failure evidence. Provider-specific enrichment is represented by rules and adapters supplied by the host; this package does not depend on a provider SDK.
