# CRM Consent and Preferences

Team-scoped consent and communication policy domain module. It owns lawful-basis proof, subscription consent, channel/topic preferences, quiet-hour metadata, suppression, recording consent, expiry, withdrawal, and persisted policy evaluations.

Subjects use opaque `subject_type` and `subject_id` references so the module does not depend on another module's private models. The policy evaluator fails closed when consent is missing, expired, withdrawn, denied, or suppressed.
