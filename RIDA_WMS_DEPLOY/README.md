# RIDA RWIMS

RIDA RWIMS is a Laravel water-resources and infrastructure management application. It includes borehole records, prospecting, dams and inspections, piped-water schemes, irrigation schemes and maintenance, water points, support tickets, client accounts, invoices, water-quality and operational reports.

## Requirements

- PHP 8.2 or later with `ctype`, `dom`, `fileinfo`, `gd`, `mbstring`, `pdo_pgsql`, `pgsql`, `simplexml`, `xml`, `xmlreader`, `xmlwriter`, and `zip`
- Composer 2
- Node.js 20 or later and npm
- PostgreSQL 18 (configure the host and port for your local/server installation)

## Local setup

1. Install PHP and JavaScript dependencies:

   ```powershell
   composer install
   npm ci
   ```

2. Create the local environment file if it does not exist, then create an application key:

   ```powershell
   Copy-Item .env.example .env
   php artisan key:generate
   ```

3. Create PostgreSQL databases named `RIDA_WMS` and `RIDA_WMS_TEST`. Configure `.env` with `DB_CONNECTION=pgsql`, `DB_HOST=localhost`, the PostgreSQL port, `DB_DATABASE=RIDA_WMS`, the working database login, and `DB_PASSWORD`. Grant that role `CREATE` on the `public` schema (or make it the database/schema owner) so Laravel migrations can create tables. Keep the test database separate from the operational database. PHPUnit targets `RIDA_WMS_TEST`; CI provisions its own disposable PostgreSQL service.

4. Apply migrations before seeding:

   ```powershell
   php artisan migrate
   ```

5. Configure a unique `@rida.gov.zw` administrator email and strong password using `INITIAL_ADMIN_EMAIL` and `INITIAL_ADMIN_PASSWORD` in `.env`. Seed only the bootstrap account; this command is safe to repeat and does not reset an existing password by default:

   ```powershell
   php artisan db:seed --class=UserSeeder
   ```

   For an explicitly approved password reset on an existing admin, set `INITIAL_ADMIN_RESET_PASSWORD=true` for one seed run. Remove all bootstrap values/flags after seeding. No default password is committed to source.

6. Build assets and start the app:

   ```powershell
   npm run build
   php artisan serve
   ```

   Open `http://127.0.0.1:8000`.

## Production deployment

- [Windows Server/IIS deployment runbook](DEPLOYMENT-WINDOWS.md)
- [Linux/Ubuntu/Nginx deployment runbook](DEPLOYMENT-LINUX.md)

Both runbooks require a reserved public address, a DNS name pointing to it, HTTPS, restricted database access, tested backups/restores, and a staging sign-off. Do not publish a production login over plain HTTP at a raw public IP.

Public self-registration is disabled. An administrator creates additional accounts and assigns their roles.

## Roles

- **Administrator:** system-wide read/write, user management, finance and stores. Cannot approve their own records or stock requests.
- **Supervisor / Provincial Engineer:** captures records and independently reviews/approves infrastructure, prospecting and stock requests. This role is currently system-wide, not province-scoped.
- **Field Officer (operator):** captures records and can view/update records they created; deletions are restricted to their own records or administrators.
- **Finance:** manages clients, invoices and payments; cannot capture or approve infrastructure or manage inventory.
- **Stores:** manages inventory, suppliers and categories and issues approved stock; cannot approve requests or access client/invoice management.
- **Viewer:** read-only access to published/shared asset records, inventory, and reports; cannot create, edit, delete, approve, request/issue stock, submit usage, or use client/invoice/supplier management.

The exact record visibility and exceptional workflows are enforced by controllers and feature tests; hiding a navigation link is not an authorization control.

## Password recovery and account passwords

- **Forgot Password:** the sign-in link opens a guest-safe form. Active accounts can request a single-use email reset link, valid for 60 minutes with a 60-second per-account interval and a five-requests/minute IP limit. Unknown, archived and inactive accounts receive the same acknowledgment but no notification.
- **Mail must actually be configured:** `MAIL_MAILER=log` and `array` do not deliver email. The form reports this explicitly and directs users to an administrator; it does not claim an email was sent. Configure a supported SMTP/provider transport, its sender and credentials privately in deployment configuration, and set `APP_URL` to the reachable application address. Clear/rebuild Laravel's config cache through the deployment process after environment changes. Verify delivery to an authorized test mailbox before relying on recovery. A selected transport on the status page is not proof of delivery.
- **Change your own password:** use the top-right account menu -> Change password. Every role can do this. The current password is required; new passwords require at least 12 characters, uppercase/lowercase letters and a number. Outstanding reset links, remember-me tokens and other sessions are revoked; the current session is rotated and retained.
- **Administrator-assisted recovery:** Users -> Edit a different active user -> Account recovery -> Issue temporary password. The administrator must verify the account holder's identity and confirm their own current password. The generated password is displayed only in the no-store issuance response, never written to the audit log or credential files. Share it privately. It expires after 24 hours, signs out existing sessions, invalidates reset links and forces a password change before other modules can be used.
- General user editing no longer accepts password changes; it links to the dedicated recovery/change flows. Self-recovery uses email or Change password, not the admin temporary-password action.
- Email reset/change/temporary issuance is audited without passwords or hashes. Database sessions are removed on password changes; a persisted password-version check also rejects old sessions on other session drivers at their next request.
- If every administrator is locked out and mail delivery is unavailable, an authorized server operator can use the bootstrap `UserSeeder` one-time reset flag described above. It now uses the same audited password update/session-revocation service. Remove bootstrap secrets and the reset flag afterward.

## System settings

Administrators can open **System settings** from the Administration navigation (`/settings`). Saving requires their current password and records the previous/new non-secret values in `audit_logs`. Other roles cannot read or change the page.

Editable values are organization/application name, support email, idle timeout (5-120 minutes), default list page size (10/20/25/50/100), the captured cost limit and water-quality screening thresholds. Settings are persisted in `system_settings`, independent of environment/config-cache rebuilds. Organization/support changes appear on login/recovery pages; the name also appears in the authenticated navigation and browser titles. The page size applies to paginated registries, including users/activity; a validated borehole `per_page` override is limited to 1-100.

Policy controls can **tighten**, not relax, the approved UAT ceilings: capture costs at most 100,000, pH within 6.5-8.5 with minimum <= maximum, TDS at most 600 mg/L and turbidity at most 5 NTU. The capture limit applies to borehole USD costs, inventory unit costs, invoice unit prices/tax, scheme funds and dam-maintenance amounts; it is not a total-invoice or project-budget cap. Existing amounts/assays are not rewritten. Changing screening thresholds invalidates report caches and recalculates current compliance under the new policy, including piped-scheme results. Stricter cost limits can require correcting an existing amount before an edited record is saved.

Read-only status includes environment, mail transport/delivery readiness, application URL/timezone, reset-token expiry/throttle and session storage. Database passwords, `APP_KEY` and mail-provider secrets are never editable or displayed in web settings. Deployment, HTTPS and operational email configuration remain server-operator responsibilities.

Responsive navigation/styles already exist. These account/settings changes do not constitute a full mobile-device audit of infrastructure forms, maps or dashboards; that work remains a separate scope.

## UAT preparation and data safeguards

On an existing **non-production** installation, use the backup-first preparation command rather than resetting or refreshing the database:

```powershell
php artisan uat:prepare
php artisan uat:prepare --apply --keep-admin=admin@rida.gov.zw
```

The first command is a no-change dry run. The second requires an existing active official-domain administrator. It verifies a private database backup, applies additive migrations, archives obvious faker/test accounts and known duplicate local System Administrator accounts, repairs only recognizable seeded assets, and adds workshop scenarios. Other named people, original creators, existing passwords and non-sample asset values are retained. Historical sample self-approvals are returned to pending independent review, with the previous approval details preserved in the audit log; valid approvals are retained. Changes are audited. Archived accounts cannot sign in; their names remain available on historical records.

PostgreSQL requires `pg_dump` on PATH, or `PG_DUMP_PATH` pointing to its executable. Backups are stored in `storage/app/private/uat-backups`; restrict access and keep them outside the public web root. A nonempty backup is checked before changes, but a separate restore drill is still required. Never run `migrate:fresh` or reset the operational/UAT database for this process.

Workshop scenarios include six inventory items (including low stock), five invoices covering draft/issued/partial/paid/overdue cases, six planned/completed dam-maintenance records, and three prospecting workflow examples. Repeating preparation does not reset account passwords or duplicate these scenarios. New demonstration credentials are written only to private storage; distribute them securely and rotate before production.

### Capture and reporting rules

- New/edited borehole costs are USD 0-100,000. Known sample boreholes use USD 1,000-5,000. Inventory unit costs, invoice unit prices/tax, revolving funds and dam-maintenance costs also have explicit upper bounds; these are UAT input guardrails, not approved budgets.
- Executive investment totals include USD boreholes only. No invented period trends, budget comparisons or static regional funding recommendations are displayed.
- Lists, reports, maps and completeness calculations use the same recorded depth/yield, including a genuine zero reading. Completeness requires coordinates, depth and yield.
- Quality screening requires pH 6.5-8.5, TDS 0-600 mg/L and turbidity 0-5 NTU. Missing readings do not count as compliant. These three measurements **do not certify drinking-water safety**; the dashboard flags turbidity above 5 NTU.
- Province, District and Ward cascade from the local administrative reference. If GPS is supplied, both coordinates are required and must fall within the selected district polygon in Zimbabwe. Invalid legacy locations must be corrected explicitly rather than silently assigned invented field coordinates.
- Prospecting follows Submitted -> Approved -> Drilled with recorded actors/timestamps and an independent approver. Drilling cannot predate approval or be in the future. A linked borehole must use the approved survey location; the link cannot be reassigned by editing the borehole. Same-day steps are permitted only in the correct chronological order.
- Functionality uses one vocabulary: `Functional`, `Partially Functional`, `Non Functional`.
- Borehole-type water points require a unique link to an existing borehole and matching location/GPS. They are service records, not additional boreholes. The water-point registry supports search, location/status/source filters and filtered CSV export.
- List deletion requires confirmation, soft-deletes the record and writes an `audit_logs` entry in the same transaction. Account activity records actual login/access; historical missing login dates are not fabricated.
- Invalid pre-existing prospecting history is returned to Submitted with its previous values audited. A responsible officer must correct and resubmit the survey before independent approval.

### Administrative boundary provenance

`resources/data/zimbabwe.json` is derived from [Zimbabwe administrative boundaries on HDX](https://data.humdata.org/dataset/cod-ab-zwe), provided by Zimbabwe National Statistics Agency / Central Statistics Office and distributed through OCHA. The source validity date is **11 September 2018**. Attribution: Zimbabwe National Statistics Agency, Central Statistics Office / OCHA COD-AB Zimbabwe. Used under [CC BY 3.0 IGO](http://creativecommons.org/licenses/by/3.0/igo/legalcode).

The local reference contains 10 provinces, 91 districts and 1,961 wards. District polygons are simplified at approximately five-metre scale and rounded to seven decimal places; ward names/parent relationships and source center points are retained. GPS is checked against district polygons, not ward polygons. This reference is not survey-grade and may not reflect later administrative changes; verify boundary-edge disputes and update the source before operational sign-off.

To rebuild after downloading and extracting the source GeoJSON package:

```powershell
node scripts\import-zimbabwe-boundaries.mjs "C:\path\to\extracted\geojson"
```

The input directory must contain `zwe_admin2.geojson` and `zwe_admin3.geojson`. Review resulting boundary changes and run the regression suite before deployment.

## Verification

```powershell
php artisan test
composer audit
composer validate --no-check-publish
npm audit
npm run build
php artisan route:list
```

Automated tests use the separate PostgreSQL `RIDA_WMS_TEST` database. They do not replace browser/device testing, load testing, or an operational backup/restore drill.

## Production release checklist

- Set `APP_ENV=production`, `APP_DEBUG=false`, the production `APP_URL`, a unique `APP_KEY`, and secure `SESSION_SECURE_COOKIE=true` behind HTTPS.
- Use PostgreSQL in production and run migrations through a reviewed deployment process after taking a database backup.
- Configure a real SMTP/provider mail transport, durable queue workers, shared cache/session storage for multi-instance deployments, and a private persistent document-storage disk.
- Configure scheduled database and uploaded-file backups; test restore into a separate environment and document recovery objectives.
- Configure centralized error monitoring, security/update alerts, log retention, health checks, and least-privilege runtime/migration DB and deployment accounts.
- Confirm ownership/privacy rules for each province/district reporting boundary with RIDA before granting wider roles. Current operator ownership is record-creator based; it is not a substitute for an approved geographic access policy.
- Do not run fake/demo seeders or import test fixtures into the production database.

## Known limitations

Inventory catalog, supplier records, stock requests, approvals/issues, usage reports, and stock movement history are usable; purchase-order creation and purchase-order-linked goods receipt remain unimplemented. Piped-water maintenance capture is available, but scheme linkage/reporting rules still need operational sign-off. The executive dashboard no longer presents an unapproved cost-savings benchmark; other financial projections still require business approval and are not audited accounting results. Water-quality screening is limited to recorded parameters and is not full potable-water certification. Invoice workflows cover client accounts, itemized invoices, payment capture, and balances; tax, receipts, aging, accounting integration, and reconciliation rules still require business approval. Operator visibility is creator-owned today; RIDA must approve province/district access boundaries before wider roles are granted. No production backup/restore drill or external load test has been completed in this workspace.
