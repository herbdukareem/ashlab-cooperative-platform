# Ashlab Cooperative Platform

A configurable, multi-tenant cooperative operations platform for membership, contributions, savings, loans, repayments, payouts, accounting, reporting and member self-service.

This repository contains the Phase 1 platform foundation and Phase 2 membership module:

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

## Quick start with Docker

Requirements: Docker Engine with the Compose plugin and Make.

```bash
make setup
make up
```

The API becomes available at `http://localhost:8080/api/v1`.

Before setup, change `PLATFORM_ADMIN_EMAIL` and `PLATFORM_ADMIN_PASSWORD` in `.env.example` or in the generated `.env`. Never use the example password in a deployed environment.

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
- `database/migrations` — MySQL-compatible Phase 1 schema
- `database/seeders` — permission catalogue and platform administrator
- `tests/Feature` — authentication, onboarding and isolation tests
- `docs` — architecture and API reference

## Security posture

This is the first implementation milestone, not yet a production release. Before real member or financial data is introduced, complete the deployment hardening, secrets management, MFA, backup restoration testing, penetration testing, privacy review and payment-provider certification work described in the product plan.

## Next milestone

Phase 3 will add contribution plans, obligations, collections, savings products, withdrawals and member financial statements.
