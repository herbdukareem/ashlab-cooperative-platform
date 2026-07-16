# Architecture

## Product boundary

The platform is a multi-tenant cooperative operations system. A cooperative is the tenant boundary. Every tenant-owned record carries `cooperative_id`, and tenant-aware models apply an automatic query scope after the authenticated tenant has been resolved.

## Phase 1 modules

- Platform administration
- Cooperative onboarding
- Branch management
- Authentication with Laravel Sanctum
- Tenant-scoped roles and global permission definitions
- Cooperative settings
- Append-only audit activity
- MySQL persistence
- Redis cache and queues

## Phase 2 membership modules

- Tenant-scoped member categories
- Concurrency-safe membership-number sequencing
- Member registration and controlled status transitions
- Encrypted NIN, BVN and other identification values
- Keyed hashes for tenant-level duplicate detection without exposing identifiers
- Private KYC document storage with checksums
- Protected bank-account numbers and verification state
- Beneficiary allocation rules
- Internal and external guarantors with consent tracking

## Request flow

1. Sanctum authenticates the user.
2. `ResolveTenant` selects the user's cooperative. Platform administrators must explicitly provide `X-Cooperative-ID` for tenant routes.
3. The tenant context is set before model binding and controller execution.
4. `BelongsToTenant` scopes tenant-owned queries and stamps new records.
5. Permission middleware authorises the requested operation.
6. Model lifecycle hooks write audit activity with sensitive values redacted.
7. The context is cleared after the response to prevent leakage in long-running workers.

## Financial design rules for later phases

- Monetary amounts are stored as integer minor units, never floating-point values.
- Every financial operation runs inside a database transaction.
- Ledger entries are immutable; corrections use reversal entries.
- External payout requests use idempotency keys and verified provider callbacks.
- Approval and release duties are separated.
- Balances are derived from controlled transactions and periodically reconciled.

## Module organisation

As the system grows, business behaviour will live under `app/Domain/<Module>` with Actions, Data objects, Events, Exceptions, Models, Policies, Queries and Services. HTTP controllers remain thin orchestration layers.
