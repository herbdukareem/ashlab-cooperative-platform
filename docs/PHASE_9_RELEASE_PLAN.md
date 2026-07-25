# Phase 9 release and pilot plan

## Environments

| Environment | Data | Integrations | Purpose |
|---|---|---|---|
| CI | Generated fixtures | Sandbox/fakes | Migrations, tests, lint and dependency audit |
| Staging | Sanitised pilot copy | Sandbox | UAT, training, reconciliation and recovery drills |
| Production | Approved member data | Live only after sign-off | Controlled pilot operations |

Production activation requires written business-owner approval, technical sign-off, successful backup restoration, provider webhook verification, balanced accounting evidence and a named incident owner. `INTEGRATION_MODE=live` alone is insufficient; the separate live-integration gate must also be enabled.

## End-to-end acceptance journeys

1. Onboard a cooperative, branch, users, roles and accounting defaults.
2. Register a member; verify KYC, bank account, beneficiary and guarantor controls.
3. Enrol contributions, collect money and confirm savings and journals.
4. Apply for a loan, assess, approve, disburse through sandbox and confirm the webhook.
5. Collect partial and full repayments; verify allocation, arrears and reversal treatment.
6. Request and approve a savings withdrawal; verify payout and reconciliation.
7. Import a bank statement, reconcile it and reproduce the trial balance.
8. Confirm member portal data, notification preferences and tenant isolation.
9. Simulate transfer failure, duplicate webhook, reversal, queue retry and database restore.

Each journey needs tester, date, evidence link, expected result, actual result and approval status. No critical or high-severity defect may remain open at launch.

## Pilot data procedure

1. Export source data to the versioned CSV template.
2. Remove unused columns and never place raw NIN/BVN values in tickets or Git.
3. Run `php artisan pilot:validate-members path/to/members.csv`.
4. Resolve every validation issue before implementing an approved import mapping.
5. Import first into staging, reconcile counts and balances, then obtain business-owner sign-off.
6. Take a production backup immediately before the controlled import.

The Phase 9 validator is deliberately read-only. Production import is deferred until the first cooperative’s source fields, opening balances and formal reconciliation rules are supplied.

## Launch evidence

- CI commit and immutable image digests
- migration and rollback rehearsal
- dependency audit and application test results
- tenant-isolation test results
- payout-provider sandbox certification
- NIN/BVN provider sandbox certification
- opening-balance reconciliation signed by the treasurer/accountant
- backup restore time and restored checksum evidence
- UAT sign-off and training attendance
- support roster, escalation contacts and rollback decision owner
