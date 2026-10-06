# RIDA RWIMS User Manual

**System:** RIDA RWIMS (Rural Water and Infrastructure Management System)  
**Audience:** RIDA administrators, operators, viewers, and deployment staff  
**Last updated:** 28 September 2026

## 1. What the system does

RIDA RWIMS is a browser-based system for recording and reviewing water and rural infrastructure assets, operational activity, inventory, support requests, clients, and invoices. It stores application data in PostgreSQL. Users sign in through the web application; access is controlled by their assigned role and, for many operator workflows, by who created the record.

The application brings operational records and reporting together. It does not replace field verification, approved financial/accounting processes, procurement controls, or an organization-wide geographic access policy.

## 2. Signing in and getting around

1. Open the web address supplied by your system administrator.
2. Sign in with the account created for you. Public self-registration is disabled.
3. Use the navigation menu to open a module. The Dashboard is the normal starting point.
4. In a list, use the available search or filters, open a record to review its details, and use the page's create/edit/action controls when your role permits.
5. Sign out when finished, especially on shared computers.

If you cannot sign in, a page is forbidden, or an action is unavailable, contact the system administrator. Do not share passwords or use another person's account.

### Roles and access

- **Administrator:** manages users and has system-wide access, including approvals, inventory decisions, client accounts, and invoices. Some actions, such as voiding an invoice, remain subject to workflow rules.
- **Operator:** enters operational records and can work with records they created where the module applies creator-based ownership. Operators may not perform administrator-only approvals or financial/user-management actions. Deletion is restricted to an operator's own records or an administrator.
- **Viewer:** reads permitted shared/published asset information, inventory, and reports. A viewer cannot create, edit, delete, approve, request or issue stock, submit usage, or use supplier, client, or invoice management.

Access is checked by the application, not just by which links appear in the menu. Operator visibility is currently based on record creator in relevant workflows; it is not a province- or district-based access boundary.

## 3. Module guide

### Dashboard

The Dashboard summarizes available operational records and indicators. Use it for a quick overview, then open the relevant asset, report, or workflow to investigate a value. Dashboard figures depend on the records entered and the user's permitted visibility; they are not a substitute for reviewing source records.

### Prospecting

Use Prospecting to record a proposed drilling location and its survey information.

1. Choose **Prospecting > New Site Survey**.
2. Enter the required location (village, ward, district, and province) and survey date. Add project name, coordinates, siting agency, prospector, and remarks when known.
3. Save the site. The system generates a reference and submits the record for review.
4. An administrator opens the record and records an **approved** or **rejected** decision with an optional review note.
5. An approved site can be marked **drilled**. A rejected site cannot be marked drilled.

A prospecting record and a borehole record are separate records. Marking a site drilled does not itself create or populate a borehole record; enter the borehole separately after drilling and retain the prospecting reference for traceability.

### Boreholes

Use Boreholes to maintain the borehole asset record, review its details, and access associated exports.

1. Open **Boreholes > New Borehole** and complete the form with verified field information.
2. Save and review the borehole detail page. Correct data through the edit action when permitted.
3. An administrator can approve or unapprove records where those controls are available.
4. Use **Borehole Maps** to view mapped locations, and the borehole report/certificate or Excel export controls to produce available documents.

Confirm coordinates and measurements against field records before using them in planning or reporting. The water-quality reporting available here is limited to measurements stored on borehole records.

### Geographic maps

The geographic map displays mapped borehole information. Use it to inspect distribution and open the underlying records where available. A point will not be useful if its coordinates are missing or incorrect. Map visibility follows the application's record-access rules; it does not establish a formal province/district boundary policy.

### Dams

Use Dams to maintain dam asset information and review associated operational pages.

- **All Dams / New Dam:** browse or enter a dam record.
- **Dam Levels:** review recorded level information where present.
- **Dam Inspections:** review inspection information where present.
- **Maintenance:** use the dam maintenance actions under the relevant dam to record and review maintenance entries.
- **Export:** use the available dam export action when a report extract is needed.

Keep inspection and maintenance entries current and tied to the correct dam. Do not assume an empty inspection or level view means the dam has been inspected; it may mean no corresponding data has been entered.

### Piped-water schemes

Use **Water Schemes** to maintain piped-water scheme records and inspect their network and water-quality views.

1. Open **All Schemes** to search and review existing schemes, or **New Scheme** to enter one.
2. Open a scheme to review its stored details and update it when permitted.
3. Use **Scheme Networks** and **Water Quality** to inspect the information currently recorded for schemes.

The scheme record, networks, and quality pages are available, but a complete piped-water scheme maintenance input workflow is not implemented. Do not use these pages as a substitute for a formal maintenance register.

### Irrigation schemes and maintenance

Use **Irrigation Schemes** to record and browse irrigation assets. Create a scheme from **New Scheme**, then open its detail page to review the saved information. Use **Irrigation Maintenances** to create and review maintenance records associated with irrigation operations. Select the correct scheme and enter verified work details and dates. The available maintenance module is distinct from the piped-water scheme pages.

### Water points

Use **Water Points** to record and browse water-point assets. Create a record from the module's create action, provide accurate location and asset information requested by the form, then open the saved record to verify it. Maintain the coordinates and status when field conditions change.

### Consumables and inventory

This area tracks the consumables catalogue and stock workflows.

- **Categories:** organize consumables into categories.
- **Inventory:** maintain consumable descriptions and stock information; open an item to review its details.
- **Suppliers:** maintain supplier records for reference.
- **Stock Requests:** an eligible user submits a request for items and quantities. An administrator reviews it and can approve or reject it; approved requests can be issued. Follow the on-screen status and do not treat an approval as proof that goods were physically handed over.
- **Stock adjustments:** administrators use the adjustment action to correct recorded stock when authorized. Enter adjustments carefully and retain the supporting evidence required by local procedure.
- **Usage Reports:** submit usage against the relevant consumable, then use the report and export views to review recorded consumption.
- **Movement history/performance:** use available stock-movement and consumables-performance views to trace changes and summarize usage.

The movement ledger supports stock traceability. Purchase-order creation and purchase-order-linked goods receipt are not implemented, so the system is not a complete procurement or receiving system. Do not record received goods as if a purchase order had been processed here.

### Clients and invoices

Client accounts and invoicing are administrator-only.

1. Create or update a client account and ensure it is active before invoicing.
2. Create an invoice for an active client. Enter the issue date, optional due date, three-letter currency, at least one item, quantities, unit prices, and any approved tax amount.
3. Save as a draft for review, or create as issued according to the organization's approval process. A draft can be issued using the issue action.
4. Record payments only against an issued invoice. Enter the date, amount, method, and any reference. A payment cannot exceed the outstanding balance.
5. Review the invoice status and balance on its detail page. An invoice with a payment cannot be voided.

The system calculates line amounts, subtotal, tax amount, total, recorded payments, and remaining balance. Tax policy, receipt generation, aging rules, accounting integration, and reconciliation procedures require separate business approval; the invoice module is not a full accounting package.

### Support tickets

Any signed-in user can submit a ticket for a technical, maintenance, access, data, or other issue.

1. Open **Ticketing > New Ticket**; enter a concise title, category, priority, and description.
2. Submit and note the generated ticket number.
3. Users can review their own tickets. An administrator can view all tickets, assign a ticket, change its priority/status, and record resolution details.
4. Use the ticket detail page to follow progress. Resolved or closed tickets record a resolution timestamp.

Do not put passwords, database credentials, or other secrets in ticket descriptions.

### Users and activity

Administrators use **Users** to create accounts, assign roles, update user information, activate/deactivate accounts, and review role and activity pages. There is no public sign-up page. Create an individual account for each person and grant only the access needed for their work. Protect the final active administrator account; the system prevents removing or disabling the last active administrator.

### Reports and analytics

Use **Executive Dashboard**, **Operational Performance**, **Technical Analysis**, **Geographic Distribution**, **Financial Reports**, **Consumables Performance**, and **Analytics Dashboard** to summarize stored data. Borehole and dam pages also provide relevant exports; report pages expose the export actions available for that report.

Reports reflect data entered into the system and the current user's permitted visibility. Check the report's filters, reporting period, and source records before sharing it. Financial reports do not supply unapproved accounting or tax treatment.

## 4. Good data practice

- Enter information from an approved source or verified field record; distinguish unknown values from zero values.
- Check dates, units, coordinates, names, and the selected parent asset before saving.
- Search for an existing asset before creating a duplicate.
- Keep identifiers/references in supporting documents so records can be traced back to field evidence.
- Correct mistakes through the relevant edit or adjustment workflow, preserving any approval and audit requirements.
- Never use the live database for training or test records. Use a separate test environment.
- Treat exported files as controlled records; share them only with authorized recipients.

## 5. Software and technology stack

- **Application framework:** Laravel 12 (PHP web framework).
- **Server language/runtime:** PHP 8.2 or later.
- **Database:** PostgreSQL 18 for the configured deployment.
- **Frontend/build:** Blade templates, JavaScript, Sass/CSS, Bootstrap 5, Tailwind CSS 4, and Vite 7.
- **PHP packages:** Composer 2; Laravel UI for authentication, Dompdf for PDF generation, and Laravel Excel/Maatwebsite for spreadsheet exports.
- **Frontend packages:** Node.js 20 or later and npm, used to install/build frontend assets.
- **Tests:** PHPUnit 11 through Laravel's `php artisan test` command.
- **Operations:** Git, a supported web browser, and PostgreSQL administration tools such as pgAdmin are useful. pgAdmin is optional; PostgreSQL itself is required.

Required PHP extensions include `ctype`, `dom`, `fileinfo`, `gd`, `mbstring`, `pdo_pgsql`, `pgsql`, `simplexml`, `xml`, `xmlreader`, `xmlwriter`, and `zip`.

## 6. Deployment requirements

A deployment needs:

- A supported Linux server (recommended for production) or a managed PHP hosting environment, with Nginx or Apache and PHP-FPM/mod_php configured for Laravel.
- PHP 8.2+ with the extensions above, Composer 2, and PostgreSQL 18 reachable from the application server.
- Node.js 20+ and npm on the build machine to compile assets. Node.js does not need to be exposed as the production web server.
- HTTPS with a valid certificate, a production hostname, DNS, and firewall rules that expose HTTPS to users while restricting PostgreSQL to trusted application/admin hosts.
- A private `.env` configuration, secure application key, least-privilege database credentials, writable Laravel `storage/` and `bootstrap/cache/` directories, and durable protected backups.
- A mail provider if password reset or outbound email is enabled. Configure queue workers, shared cache/session services, centralized logs/monitoring, and persistent private document storage when required by the deployment scale and enabled features.

The application does not require Docker. Do not put `.env`, database passwords, bootstrap administrator credentials, or production backups in source control or a public web directory.

## 7. Deployment process

Use a reviewed release and a maintenance window for production changes. Adapt service names and paths to the server's operating system and hosting provider.

1. **Prepare infrastructure.** Install PHP and required extensions, Composer, PostgreSQL, a web server, and TLS. Create the production database and a dedicated database role. Grant only the required database/schema privileges. Keep PostgreSQL off the public internet.
2. **Back up before changing production.** Take and verify a PostgreSQL backup and back up any uploaded/private files. Know the restore procedure before deploying migrations.
3. **Install the release.** Check out the approved application version into a release directory. Do not deploy `vendor/`, `node_modules/`, `.env`, or user-uploaded files from an untrusted source.
4. **Configure secrets.** Create a private production `.env` with `APP_ENV=production`, `APP_DEBUG=false`, the real `APP_URL`, `DB_CONNECTION=pgsql`, production DB connection values, secure session/cookie settings, and the approved mail/storage/cache/queue configuration. Generate a unique `APP_KEY` once for the installation and preserve it across releases.
5. **Install dependencies and build assets.** From the application root:

   ```sh
   composer install --no-dev --prefer-dist --optimize-autoloader
   npm ci
   npm run build
   ```

6. **Apply database changes.** After reviewing the release migrations and confirming the backup, run:

   ```sh
   php artisan migrate --force
   ```

   Never point tests at the production database. PHPUnit is configured to use the separate `RIDA_WMS_TEST` database.
7. **Create the initial administrator safely.** Set a unique `INITIAL_ADMIN_EMAIL` and strong `INITIAL_ADMIN_PASSWORD` through a protected environment/secret mechanism, run the documented user seeder once, then remove the bootstrap values. Do not use a shared/default password. Only set `INITIAL_ADMIN_RESET_PASSWORD=true` for an explicitly approved password reset, and remove it immediately afterward.
8. **Set permissions and web root.** Allow the web-server account to write to `storage/` and `bootstrap/cache/`. Configure the web server's document root to Laravel's `public/` directory, enable HTTPS, and deny direct access to project files, `.env`, logs, and backups.
9. **Switch traffic and verify.** Clear/rebuild Laravel caches as appropriate for the release, reload PHP/web-server processes, sign in with the bootstrap administrator, check the health endpoint and key modules, verify logs, and confirm that reports and database writes work.
10. **Operate and recover.** Monitor application and PostgreSQL health, apply security updates, retain logs, and run scheduled encrypted backups for both database and private files. Test restoring a backup into a separate environment. Keep a rollback/recovery plan for failed deployments.

For local development, install dependencies with `composer install` and `npm ci`, configure `.env` for a separate PostgreSQL development database, run migrations, build assets with `npm run build`, then use `php artisan serve`. The development server is not a production web server.

## 8. Testing and release checks

Run checks against a dedicated test database, never the operational database:

```powershell
php artisan test
composer validate --no-check-publish
composer audit
npm audit
npm run build
php artisan route:list
```

Automated tests help verify role and workflow behavior but do not replace browser/device checks, load testing, operational security review, or a successful backup-restore drill. Do not load mock/test records into production.

## 9. Current scope and limitations

- Piped-water scheme maintenance entry is not a complete workflow.
- Purchase-order creation and purchase-order-linked goods receipt are not implemented.
- Borehole water-quality reporting only covers measurements stored on borehole records.
- Invoice tax treatment, receipts, aging, accounting integration, and reconciliation rules need approved business requirements.
- Operator ownership is creator-based; province/district access policy has not been configured.
- Production backup/restore, browser compatibility, and load testing must be completed by the deploying organization.
