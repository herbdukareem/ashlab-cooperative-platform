# Ashlab Cooperative Platform

A configurable, multi-tenant cooperative operations platform for membership, contributions, savings, loans, repayments, payouts, accounting, reporting and member self-service.

This repository contains the platform foundation through the Phase 4 credit-configuration engine:

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

This is an implementation milestone, not yet a production release. Before real member or financial data is introduced, complete deployment hardening, secrets management, MFA, backup restoration testing, penetration testing, privacy review and payment-provider certification.

## Next milestone

Phase 8 will add the administration and member web applications, mobile experience, notifications, operational dashboards and pilot deployment hardening.
