# Phase 1 API

Base URL: `/api/v1`

Authenticated requests use `Authorization: Bearer <token>`. Tenant routes derive the cooperative from the authenticated user. A platform administrator must send `X-Cooperative-ID` when using a tenant route.

| Method | Endpoint | Permission | Purpose |
|---|---|---|---|
| POST | `/auth/login` | Public | Issue a Sanctum token |
| GET | `/auth/me` | Authenticated | Return current user, roles and permissions |
| POST | `/auth/logout` | Authenticated | Revoke the current token |
| GET | `/platform/cooperatives` | `platform.cooperatives.manage` | List tenants |
| POST | `/platform/cooperatives` | `platform.cooperatives.manage` | Onboard a cooperative, head office and administrator |
| GET | `/branches` | `branches.manage` | List tenant branches |
| POST | `/branches` | `branches.manage` | Create a branch |
| GET | `/roles` | `roles.manage` | List tenant roles |
| POST | `/roles` | `roles.manage` | Create a role and assign permissions |
| GET | `/permissions` | `roles.manage` | List the permission catalogue |
| GET | `/users` | `users.manage` | List cooperative users |
| POST | `/users` | `users.manage` | Create a cooperative user |
| GET | `/settings` | `settings.view` | Read cooperative settings |
| PUT | `/settings` | `settings.manage` | Update cooperative settings |
| GET | `/audit-logs` | `audit.view` | Filter and review tenant audit activity |
| CRUD | `/member-categories` | `members.categories.manage` | Configure membership categories |
| GET/POST | `/members` | `members.view` / `members.create` | Search or register members |
| GET/PUT | `/members/{member}` | `members.view` / `members.update` | Read or update a member |
| PATCH | `/members/{member}/status` | `members.approve` | Apply an allowed status transition |
| POST | `/members/{member}/identifications` | `kyc.manage` | Add an encrypted KYC identifier |
| PATCH | `/members/{member}/identifications/{id}/verify` | `kyc.verify` | Verify or reject an identifier |
| POST | `/members/{member}/documents` | `kyc.manage` | Upload a private KYC document |
| GET | `/members/{member}/documents/{id}/download` | `kyc.view` | Download an authorised document |
| POST | `/members/{member}/bank-accounts` | `kyc.manage` | Add a protected bank account |
| CRUD | `/members/{member}/beneficiaries` | `members.beneficiaries.manage` | Manage beneficiary allocation |
| CRUD | `/members/{member}/guarantors` | `members.guarantors.manage` | Manage guarantors and consent |

## Cooperative onboarding payload

```json
{
  "name": "Example Staff Cooperative Society",
  "slug": "example-staff-cooperative",
  "registration_number": "NIG/CS/00001",
  "currency": "NGN",
  "financial_year_start_month": 1,
  "admin": {
    "first_name": "Amina",
    "last_name": "Bello",
    "email": "amina@example.org",
    "password": "a-long-unique-password",
    "password_confirmation": "a-long-unique-password"
  }
}
```
