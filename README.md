# Ashlab Cooperative Platform

The platform now includes the Phase 1–9 pilot scope: tenant administration, members and KYC, contributions and savings, lending and payouts, servicing and recovery, accounting and reconciliation, a responsive staff/member PWA, notifications, sandbox provider integrations and release controls.

Open `http://localhost:8080` after setup to use the web application. See `docs/PILOT_DEPLOYMENT.md` before any pilot deployment.

A configurable, multi-tenant cooperative operations platform for membership, contributions, savings, loans, repayments, payouts, accounting, reporting and member self-service.

This repository contains the complete Phase 1–9 pilot implementation:

- Laravel 13 REST API on PHP 8.3+
- MySQL 8.4 as the primary database
- Redis-backed cache, sessions and queues
- Cooperative tenant isolation
- Sanctum API authentication
- Tenant-scoped roles and permissions
- Branch and user administration
- Cooperative settings
- Sensitive-value-redacted audit trails
- Docker-based local development
- Feature tests for login, onboarding and tenant isolation
- Member categories and automatic membership numbering
- Member registration, search, approval and status history
- Encrypted KYC identifiers with duplicate detection
- Private KYC document storage and controlled verification
- Protected member bank-account records
- Beneficiaries with 100% allocation enforcement
- Internal and external guarantors with consent status
- Configurable recurring and custom-date contribution plans
- Member enrolment, scheduled obligations, arrears and oldest-debt-first allocation
- Idempotent multi-channel collections, receipts and unallocated-payment tracking
- Configurable savings products, member accounts and immutable transaction ledgers
- Savings statements and approval-controlled withdrawals with reserved balances
- Configurable loan products, interest methods, terms and repayment frequencies
- Tenant-defined charge engine with product attachment, caps and exemptions
- Eligibility, affordability and guarantor-capacity policy evaluation
- Multi-step approval workflow definitions and repayment-schedule previews
- Loan applications, assessment snapshots, guarantor consent and workflow decisions
- Contractual loan records with persisted charges and repayment installments
- Provider-neutral, idempotent payout processing with maker/checker release controls
- Individual, bulk, scheduled, recurring, dividend, refund, welfare, supplier, withdrawal and loan-disbursement payouts
- Idempotent partial, full and advance loan repayments with component-level allocation
- Repayment reversals, automated arrears aging and configurable late penalties
- Loan restructuring, recovery cases, promises to pay and collection activity history
- Tenant chart of accounts, fiscal periods and configurable automatic posting rules
- Balanced immutable journals, reversals and member, loan and branch subsidiary dimensions
- Protected cooperative bank accounts, statement matching and reconciliation
- Trial balance, income statement and balance sheet reporting
- Signed, idempotent transfer webhooks and sandbox transfer initiation
- Replaceable NIN/BVN identity-verification contract with a safe sandbox adapter
- Production image builds, staging release workflow and live-integration kill switch
- Pilot readiness command, read-only member CSV validation and UAT release plan

## Quick start with Docker

Requirements: Docker Engine with the Compose plugin and Make.

```bash
make setup
make up
```

The API becomes available at `http://localhost:8080/api/v1`.

Before setup, set `DB_PASSWORD`, `DB_ROOT_PASSWORD`, `PLATFORM_ADMIN_EMAIL` and `PLATFORM_ADMIN_PASSWORD` in the generated `.env`. Password values are intentionally blank in `.env.example` and Docker Compose will refuse to start until database passwords are provided.

For production, set `IDENTIFIER_HASH_KEY` to an independent random secret and preserve it securely. Changing or losing it will prevent reliable duplicate checks for protected identifiers.

## Manual setup

Requirements: PHP 8.3+, Composer 2, MySQL 8.4, Redis 7 and the PHP extensions listed in `docker/php/Dockerfile`.

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Run quality checks:

```bash
composer lint
composer test
```

Validate a staging deployment and a proposed member file:

```bash
php artisan pilot:readiness --allow-sandbox
php artisan pilot:validate-members docs/templates/pilot_members.csv
```

## Repository map

- `app/Support/Tenancy` — resolved tenant context
- `app/Models/Concerns` — tenant scoping and audit behaviour
- `app/Actions` — transactional application operations
- `app/Http` — API transport, validation and authorisation
- `database/migrations` — MySQL-compatible, tenant-scoped platform schema
- `database/seeders` — permission catalogue and platform administrator
- `tests/Feature` — authentication, onboarding and isolation tests
- `docs` — architecture and API reference

## Security posture

This is a pilot release candidate, not approval to process real money. Keep integrations in sandbox until CI, UAT, restore testing, privacy/security review, provider certification and opening-balance reconciliation are formally signed off.

## Next milestone

Complete staging UAT with the first cooperative, configure approved Nigerian transfer and identity-provider adapters, then grant the separate live-integration approval only after the Phase 9 exit criteria pass.
