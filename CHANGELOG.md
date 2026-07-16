# Changelog

## Phase 4 — Credit configuration

- Added configurable loan products with principal, tenure, interest, frequency, moratorium and guarantor policies.
- Added a tenant-defined charge engine supporting fixed and percentage calculations, caps, timing, treatment, refunds and member-category exemptions.
- Added product-level charge attachment with ordering, mandatory flags and overrides.
- Added configurable hard and advisory eligibility rules.
- Added contribution, savings, membership-duration and debt-to-income credit policy evaluation.
- Added guarantor exposure and active-guarantee capacity evaluation.
- Added reusable multi-step approval workflows with amount bands and distinct-actor controls.
- Added integer-minor-unit repayment previews for flat, reducing-balance and zero-interest schedules.
- Added tenant permissions, APIs, factories and credit-engine tests.

## Phase 3 — Contributions and savings

- Added configurable fixed or flexible contribution plans with daily through annual, one-time and custom schedules.
- Added member enrolment, deterministic obligation generation, grace periods and arrears status refresh.
- Added idempotent collections with oldest-obligation-first allocation and support for mixed contribution and savings payments.
- Added payment receipts, allocation records and explicit unallocated balances.
- Added savings products, member accounts and append-only savings transactions.
- Added withdrawal limits, lock-in periods, fees, minimum-balance rules and maker/checker approval states.
- Added balance reservation at withdrawal approval to prevent double spending.
- Added paginated savings statements, tenant permissions, scheduled commands, factories and financial-flow tests.

## Phase 2 — Membership foundation

- Added configurable member categories and requirements.
- Added concurrency-safe, tenant-specific membership numbering.
- Added member registration, search, updates and controlled status transitions.
- Added encrypted identification values with masked API responses and keyed duplicate detection.
- Added private KYC document storage, checksums, verification and authorised downloads.
- Added protected bank-account records and primary-account controls.
- Added beneficiaries with total entitlement enforcement.
- Added internal and external guarantors with consent and exposure controls.
- Added membership permissions, APIs, factories and feature tests.

## Phase 1 — Platform foundation

- Added Laravel, MySQL, Redis and Docker foundation.
- Added cooperative tenancy, authentication, branches, users, roles, settings and audit logs.
