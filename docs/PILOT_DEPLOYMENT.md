# Pilot deployment runbook

The detailed acceptance matrix and evidence requirements are in `docs/PHASE_9_RELEASE_PLAN.md`.

## Release gate

1. Provision a private MySQL 8.4 database and Redis instance.
2. Set unique production secrets; never reuse development credentials.
3. Run `composer install --no-dev --classmap-authoritative`.
4. Run `php artisan migrate --force`, `php artisan db:seed --class=PermissionSeeder --force`, and `php artisan optimize`.
5. Start the web service, queue workers and scheduler under a process supervisor.
6. Confirm `/health/live` returns 200 and `/health/ready` reports both MySQL and Redis ready.
7. Test login, tenant isolation, maker/checker approvals, one collection, one payout sandbox event and journal balancing.
8. Run `php artisan pilot:readiness --allow-sandbox`; every check must pass.
9. Trigger the manual `Staging release` GitHub Actions workflow and record both immutable image tags.

## Required production settings

- `APP_ENV=production`, `APP_DEBUG=false`, HTTPS `APP_URL`
- long random `APP_KEY` and independent `IDENTIFIER_HASH_KEY`
- restrictive `CORS_ALLOWED_ORIGINS` and `SANCTUM_STATEFUL_DOMAINS`
- encrypted database backups, object-storage versioning and log retention
- provider credentials supplied through the deployment secret store
- `INTEGRATION_MODE=sandbox` until provider certification and launch sign-off
- `ALLOW_LIVE_FINANCIAL_INTEGRATIONS=false` until the final production change approval
- at least two queue workers with retry monitoring

## Pilot operating controls

- Begin with one cooperative and a limited member cohort.
- Keep payout providers in sandbox until dual approval, reconciliation and reversal drills pass.
- Reconcile bank and payout clearing accounts daily.
- Review failed jobs, readiness checks, audit logs and notification failures each morning.
- Export a daily trial balance and confirm total debits equal total credits.
- Record every issue with severity, owner, reproduction steps and resolution evidence.

## Recovery objectives

- Nightly full backup plus binary-log point-in-time recovery.
- Quarterly restore drill; mandatory restore drill before public rollout.
- Pilot targets: RPO 15 minutes, RTO 4 hours.
- Never repair financial history by editing journal, savings or allocation rows; use supported reversal flows.

## Provider callback contract

The sandbox transfer callback endpoint is `POST /api/v1/webhooks/transfers`. The provider must send the exact raw-body HMAC-SHA256 value in `X-Webhook-Signature`. Events require `event_id`, `reference` and one of `paid`, `failed`, `reversed` or `processing`. Duplicate event IDs are accepted without duplicate financial posting.
