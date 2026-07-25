# Delivery roadmap

## Milestone 1 — Platform foundation (implemented)

- MySQL-backed Laravel API
- Cooperative tenant isolation
- Authentication and API tokens
- Platform onboarding
- Branches, users, roles and permissions
- Settings and audit logs
- Docker development environment

## Milestone 2 — Membership (implemented)

- Member categories and numbering
- Member registration and approval
- KYC and document storage
- Bank accounts and name verification abstraction
- Beneficiaries and guarantors
- Member status lifecycle

## Milestone 3 — Contributions and savings (implemented)

- Configurable contribution plans
- Obligations and arrears
- Savings products and accounts
- Collection allocation and receipts
- Withdrawals with approval controls
- Member statements

## Milestone 4 — Credit configuration (implemented)

- Loan products and eligibility rules
- Configurable charge engine
- Approval workflow engine
- Guarantor capacity controls
- Transparent charge and schedule previews

## Milestone 5 — Loans and payouts (implemented)

- Loan applications, assessment and approvals
- Disbursement and repayment schedule generation
- Provider-neutral payout gateway
- Single and bulk payouts
- Idempotency, webhooks, retries and reversals
- Payout reconciliation

## Milestone 6 — Repayment and recovery (implemented)

- Repayment collection and allocation
- Bulk employer remittances
- Arrears, reminders and penalties
- Restructuring and recovery

## Milestone 7 — Accounting and intelligence (implemented)

- Chart of accounts and immutable journals
- General and subsidiary ledgers
- Bank and payout reconciliation
- Financial statements
- Management dashboards and exports

## Milestone 8 — Experience and deployment (implemented)

- Responsive web administration and linked-member portal
- Installable mobile PWA with offline application shell
- Notification inbox, preferences and push-subscription foundation
- Management and member dashboards
- Security headers, request tracing and service health checks
- Pilot deployment and recovery runbook

## Pilot exit criteria

- All migrations and automated tests pass against MySQL 8.4.
- Tenant-isolation and maker/checker tests pass with representative pilot roles.
- Payment and payout provider sandbox certification is complete.
- Trial balance, bank reconciliation and payout-clearing checks balance.
- Backup restoration, incident response and payout reversal drills pass.
- Pilot users complete acceptance testing on desktop and Android/iOS mobile browsers.
