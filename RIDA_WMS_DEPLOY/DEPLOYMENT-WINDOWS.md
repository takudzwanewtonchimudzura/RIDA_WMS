# RIDA RWIMS Deployment on Windows Server

This runbook deploys one production instance on Windows Server with IIS, PHP FastCGI, and PostgreSQL. It assumes an administrator can configure the server, public firewall/NAT, DNS, and TLS certificate. Use a staging environment first; never use the PHPUnit database for production.

## 1. Public address and network

1. Reserve a static public IPv4 address from the cloud provider or ISP. Record the address, gateway, and server's private address if the server is behind NAT.
2. Create a DNS `A` record such as `wms.example.org` pointing to the static public IP. Wait for public DNS resolution before requesting a certificate. Add an `AAAA` record only if IPv6 is routed and firewalled correctly.
3. Use `https://wms.example.org` as the production address. Do not expose logins or operational records over `http://<public-ip>`; a bare public IP is not a suitable production TLS identity. HTTP should redirect to HTTPS. If users must type the IP, direct them to the DNS name.
4. At both the provider firewall and Windows Firewall, allow inbound TCP 80 and 443. Port 80 is needed for redirect and certificate renewal. Allow RDP only from approved administrator IPs. Do not expose PostgreSQL TCP 5432, Redis, FastCGI, or application ports publicly.
5. If NAT is used, forward only 80/443 to IIS. Keep the database bound to loopback or its private interface.
6. Confirm client browsers can reach the currently referenced HTTPS asset hosts (`cdn.jsdelivr.net`, `cdnjs.cloudflare.com`, `fonts.googleapis.com`, and `unpkg.com`) and the OpenStreetMap tile service (`tile.openstreetmap.org`). The map will show markers but no usable basemap if its tile service is blocked. Review the tile provider's attribution and usage policy; procure an approved/self-hosted tile provider for material or high-volume operational use.
7. Verify the provider firewall and Windows Firewall still block PostgreSQL and remote administration from the public internet.

## 2. Install server prerequisites

1. Install a supported, fully patched Windows Server release. Use a dedicated server and a non-shared administrator account; enable time synchronization and endpoint protection.
2. In elevated PowerShell, install IIS and CGI:

   ```powershell
   Install-WindowsFeature Web-Server, Web-CGI, Web-Mgmt-Tools
   ```

3. Install the IIS URL Rewrite module. It is required by the `public\web.config` rewrite rules shipped with this application. Restart IIS after installation.
4. Install PHP 8.3 x64 Non-Thread-Safe (NTS), its matching Microsoft Visual C++ runtime, and Composer 2. Configure `php.ini` for IIS FastCGI and enable `ctype`, `fileinfo`, `gd`, `mbstring`, `pdo_pgsql`, `pgsql`, `simplexml`, `xml`, `xmlreader`, `xmlwriter`, and `zip`. Set `date.timezone` to the organisation's timezone, set `cgi.fix_pathinfo=0`, and enable OPcache. Restart IIS after changing PHP configuration.
5. Install PostgreSQL 18 and Node.js 20 LTS with npm. PostgreSQL can be on a separate private/managed host, but it must use TLS and a private network/security-group allowlist.
6. Verify versions and extensions from an elevated PowerShell prompt:

   ```powershell
   php -v
   php -m
   composer --version
   node --version
   npm --version
   psql --version
   ```

   Confirm every PHP extension listed in `README.md` is enabled in the same PHP installation that IIS FastCGI uses.

## 3. Prepare PostgreSQL roles and database

1. Connect to PostgreSQL as its administrator, for example `psql -h 127.0.0.1 -U postgres -d postgres`.
2. Create separate migration and runtime roles, then set their passwords interactively so secrets do not enter shell history:

   ```sql
   CREATE ROLE rwims_migrator LOGIN;
   CREATE ROLE rwims_runtime LOGIN;
   CREATE DATABASE RIDA_WMS OWNER rwims_migrator;
   \password rwims_migrator
   \password rwims_runtime
   \connect RIDA_WMS
   GRANT CONNECT ON DATABASE RIDA_WMS TO rwims_runtime;
   GRANT USAGE ON SCHEMA public TO rwims_runtime;
   ALTER DEFAULT PRIVILEGES FOR ROLE rwims_migrator IN SCHEMA public
       GRANT SELECT, INSERT, UPDATE, DELETE ON TABLES TO rwims_runtime;
   ALTER DEFAULT PRIVILEGES FOR ROLE rwims_migrator IN SCHEMA public
       GRANT USAGE, SELECT, UPDATE ON SEQUENCES TO rwims_runtime;
   ```

3. Keep PostgreSQL listening only on loopback/private interfaces. For a separate database host, allow TCP 5432 only from this app server's private address, require TLS, and use the provider's verified CA configuration. Never use the database superuser in `.env`.
4. Create a separate empty `RIDA_WMS_TEST` database only on a development/CI machine. Never point `php artisan test` at `RIDA_WMS`.

## 4. Place and build the application

1. Transfer a reviewed release archive or checkout into `C:\Sites\RidaWms\app`. Do not place the project root under IIS's public web root. Only the project's `public` directory will be served.
2. From the project root, install production PHP dependencies and build static assets:

   ```powershell
   Set-Location C:\Sites\RidaWms\app
   composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
   npm ci
   npm run build
   ```

   Keep the generated `public\build` directory with the release. `node_modules` is not served by IIS.
3. Create `.env` from `.env.example` if this is a new installation. Restrict its ACL so only server administrators, the deployment identity, and the IIS application-pool identity can read it. Never copy `.env` into `public` or commit it.
4. Create or preserve `APP_KEY`. Generate a key only on first installation, before storing encrypted application data. Never regenerate it during upgrades or recovery.
5. Set the production environment before migrations:

   ```dotenv
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://wms.example.org
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=RIDA_WMS
   DB_USERNAME=rwims_migrator
   DB_PASSWORD=<migration-role-secret>
   DB_SSLMODE=prefer
   SESSION_DRIVER=file
   SESSION_SECURE_COOKIE=true
   SESSION_HTTP_ONLY=true
   SESSION_SAME_SITE=lax
   CACHE_STORE=file
   QUEUE_CONNECTION=database
   FILESYSTEM_DISK=local
   MAIL_MAILER=<approved-smtp-transport>
   MAIL_HOST=<smtp-host>
   MAIL_PORT=<smtp-port>
   MAIL_USERNAME=<smtp-user>
   MAIL_PASSWORD=<smtp-secret>
   MAIL_FROM_ADDRESS=<approved-sender>
   ```

   Set the actual mail transport and secrets supplied by the organisation. For managed PostgreSQL, use its verified TLS mode rather than `prefer`. File sessions/cache are appropriate only for this single-server deployment; multi-server deployments need shared session, cache, queue, and file storage services.

## 5. Run first-time database setup

1. Take a database backup before an existing database is changed. For a new installation, confirm the target database is `RIDA_WMS` and empty.
2. From the application root, run:

   ```powershell
   php artisan migrate --force
   ```

3. Before building the configuration cache, temporarily set unique, private bootstrap credentials in `.env`: `INITIAL_ADMIN_EMAIL` and `INITIAL_ADMIN_PASSWORD`. Use a unique administrator email and a password of at least 12 characters; do not use a shared/default password.
4. Create the initial administrator once:

   ```powershell
   php artisan db:seed --class=UserSeeder --force
   ```

   Confirm the command reports that the administrator was created. If it reports a validation or existing-account warning, stop and resolve it; do not assume a successful deployment created an admin.
5. Remove `INITIAL_ADMIN_EMAIL`, `INITIAL_ADMIN_PASSWORD`, and any `INITIAL_ADMIN_RESET_PASSWORD` value from `.env`. Do not enable the reset flag except for an explicitly approved one-time recovery. Do not run demo or test seeders in production.
6. Grant the runtime role access to the migrated tables and sequences:

   ```sql
   GRANT SELECT, INSERT, UPDATE, DELETE ON ALL TABLES IN SCHEMA public TO rwims_runtime;
   GRANT USAGE, SELECT, UPDATE ON ALL SEQUENCES IN SCHEMA public TO rwims_runtime;
   ```

7. Change `.env` to `DB_USERNAME=rwims_runtime` and its secret. Keep `rwims_migrator` credentials in the protected deployment secret store and use them only for reviewed migrations. Then cache Laravel configuration:

   ```powershell
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

## 6. Configure IIS and file permissions

1. Create an IIS application pool named `RidaRwims`, set .NET CLR to **No Managed Code**, enable 64-bit, and use a dedicated virtual identity. Do not run the pool as `LocalSystem` or an administrator.
2. Create an IIS site whose physical path is `C:\Sites\RidaWms\app\public`, with a host name of `wms.example.org` on HTTP 80. Ensure the site's `web.config` rewrite rules are enabled. Add a PHP FastCGI handler for `*.php` using the exact `php-cgi.exe` and `php.ini` installation verified in step 2. Do not configure IIS to serve `.env`, project source, `vendor`, or `storage` from outside `public`.
3. Grant the application-pool identity read/execute access to the application files and **Modify** only to `storage` and `bootstrap\cache`. Restrict `.env` read access to the same pool identity and administrators. Do not make the whole application tree writable by IIS.
4. If the app needs public uploaded files, create the public storage link once and verify its target. Keep private documents on Laravel's private local disk; do not put private operational files under `public`.
5. Set IIS request limits to match the approved upload limit. Disable directory browsing and remove unused IIS modules/features.

## 7. Bind HTTPS and publish the address

1. Confirm `wms.example.org` resolves publicly to the reserved IP. Request a certificate for the DNS name using a maintained ACME client such as win-acme, then bind it to the IIS site on port 443.
2. Configure HTTP to redirect to HTTPS and verify automatic certificate renewal. Do not install a self-signed certificate for public users and do not ask users to bypass browser warnings.
3. Confirm the site is reachable from a network outside the server's LAN at `https://wms.example.org/up`. The Laravel health endpoint must return a successful response. Ensure direct HTTP requests redirect to the HTTPS DNS name.
4. Verify the provider firewall and Windows Firewall still block PostgreSQL and remote administration from the public internet.

## 8. Start queues and the scheduler

1. Install a maintained Windows service wrapper such as NSSM from its official source. Configure a service named `RidaRwimsQueue` with:
   - Application: `C:\PHP\php.exe` (use the PHP CLI path)
   - Startup directory: `C:\Sites\RidaWms\app`
   - Arguments: `artisan queue:work database --sleep=3 --tries=3 --timeout=60`
   - Startup: automatic; recovery: restart on failure
   - Run identity: a dedicated least-privilege account with `.env` read access and write access to `storage`
2. Start the service and confirm it remains running. The queue timeout (60 seconds) must remain below the queue connection's `retry_after` value (90 seconds by default).
3. In Task Scheduler, create a task running `C:\PHP\php.exe` with arguments `artisan schedule:run` and start-in directory `C:\Sites\RidaWms\app`. Repeat every one minute indefinitely, run whether or not an administrator is logged in, and restart on failure.
4. Check Laravel logs, the queue failed-jobs table, and Windows service/task history after a queued job has been dispatched. If the application has no queued work, still verify the worker can connect to the database and run.

## 9. Backups, restore, and monitoring

1. Create a scheduled, encrypted, off-server PostgreSQL backup and an uploaded/private-file backup. Restrict backup access; retain multiple generations according to RIDA policy. Never store the only backup on this server.
2. Use `pg_dump -Fc` with a protected PostgreSQL password file or service account, not a password embedded in a command. Back up the matching persistent files under `storage\app` and the protected deployment configuration separately; keep `APP_KEY` in a secure secret manager.
3. Restore both the database and files into an isolated staging environment and verify sign-in, records, PDFs, map visibility, and role restrictions. Record recovery time and data-loss point. A backup that has not been restored is not a verified backup.
4. Configure centralized logs/error monitoring, disk-space and service alerts, Windows security updates, dependency/security advisories, and certificate-expiry alerts. Retain logs without recording passwords, tokens, or full private records.

## 10. Release verification and routine updates

1. Before a first public launch, run the checks from the release checkout against the isolated test database: `php artisan test`, `composer audit`, `composer validate --no-check-publish`, `npm audit`, `npm run build`, and `php artisan route:list`.
2. In staging, verify all capture forms with at least five records per workflow, including boreholes, prospecting, dams, piped-water schemes and their maintenance, irrigation schemes and maintenance, water points, consumables/catalog/suppliers, stock requests/issues/usage, tickets, clients, invoices, and payments. Use distinct coordinates for map testing. Do not insert these fixtures into production.
3. Verify the map on desktop and a real phone: markers, province and yield filters, popup escaping, map tiles, and location accuracy. Verify guest denial, operator ownership, viewer read-only access, admin-only screens, HTTPS cookies, TLS renewal, and no production debug output.
4. For every later release: take/verify a backup, put the app into maintenance mode, deploy reviewed code/assets, run `php artisan migrate --force` using the migration role, change back to the runtime role, clear and rebuild caches, restart the queue service, smoke-test health/login/maps/core workflows, then exit maintenance mode. Keep the previous release and backup available for rollback.
5. Keep the application in maintenance mode and do not announce launch if the TLS, restore, authentication, authorization, mapping, or business acceptance checks fail.

## Public-IP limitation and launch sign-off

A public IP is a network destination, not an HTTPS certificate identity or DNS name. Use a DNS hostname pointing to the static IP and publish that HTTPS URL. Coordinate router/provider firewalls and certificate validation with the network owner. The application still needs RIDA's sign-off on geographic access boundaries, cost-savings/reporting assumptions, and any remaining purchasing/finance requirements listed in `README.md` before those workflows are represented as complete.