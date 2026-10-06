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

5. Configure a unique administrator email and strong password using `INITIAL_ADMIN_EMAIL` and `INITIAL_ADMIN_PASSWORD` in `.env`. Seed only the bootstrap account; this command is safe to repeat and does not reset an existing password by default:

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

- **Administrator:** system-wide read/write, approvals, user management, client accounts, and invoices.
- **Operator:** creates records and can view/update records they created; deletions are restricted to their own records or administrators.
- **Viewer:** read-only access to published/shared asset records, inventory, and reports; cannot create, edit, delete, approve, request/issue stock, submit usage, or use client/invoice/supplier management.

The exact record visibility and exceptional workflows are enforced by controllers and feature tests; hiding a navigation link is not an authorization control.

## Verification

```powershell
php artisan test
composer audit
composer validate --no-check-publish
npm audit
npm run build
php artisan route:list
```

Automated tests use the separate PostgreSQL `rwims_test` database. They do not replace browser/device testing, load testing, or an operational backup/restore drill.

## Production release checklist

- Set `APP_ENV=production`, `APP_DEBUG=false`, the production `APP_URL`, a unique `APP_KEY`, and secure `SESSION_SECURE_COOKIE=true` behind HTTPS.
- Use PostgreSQL in production and run migrations through a reviewed deployment process after taking a database backup.
- Configure a real SMTP/provider mail transport, durable queue workers, shared cache/session storage for multi-instance deployments, and a private persistent document-storage disk.
- Configure scheduled database and uploaded-file backups; test restore into a separate environment and document recovery objectives.
- Configure centralized error monitoring, security/update alerts, log retention, health checks, and least-privilege runtime/migration DB and deployment accounts.
- Confirm ownership/privacy rules for each province/district reporting boundary with RIDA before granting wider roles. Current operator ownership is record-creator based; it is not a substitute for an approved geographic access policy.
- Do not run fake/demo seeders or import test fixtures into the production database.

## Known limitations

Inventory catalog, supplier records, stock requests, approvals/issues, usage reports, and stock movement history are usable; purchase-order creation and purchase-order-linked goods receipt remain unimplemented. Piped-water maintenance capture is available, but scheme linkage/reporting rules still need operational sign-off. The cost-savings report currently uses an unapproved fixed benchmark and must not be treated as an audited financial KPI. Water-quality reporting is limited to measurements stored on borehole records. Invoice workflows cover client accounts, itemized invoices, payment capture, and balances; tax, receipts, aging, accounting integration, and reconciliation rules still require business approval. Operator visibility is creator-owned today; RIDA must approve province/district access boundaries before wider roles are granted. No production backup/restore drill or external load test has been completed in this workspace.
