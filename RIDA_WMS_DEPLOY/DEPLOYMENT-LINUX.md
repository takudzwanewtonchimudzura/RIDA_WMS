# RIDA RWIMS Deployment on Linux

This runbook deploys one production instance on Ubuntu 24.04 LTS with Nginx, PHP-FPM, and PostgreSQL 18. Use a clean staging environment before production. Commands assume `sudo` access and an application DNS name that points to a static public IP.

## 1. Reserve the public address and configure network access

1. Reserve a static public IPv4 address from the cloud provider or ISP. If the host is behind a router/NAT, record its private address and configure forwarding only after the host is secured.
2. Create a DNS `A` record, for example `wms.example.org`, pointing to the static IP. Add an `AAAA` record only when the host has working, filtered IPv6. Wait until the name resolves publicly.
3. Publish and use `https://wms.example.org`, not `http://<public-ip>`. A bare public IP is not a suitable production TLS identity. Configure HTTP to redirect to HTTPS; tell users the DNS URL even if they know the address.
4. In the cloud firewall/security group and host firewall, allow inbound TCP 80 and 443. Allow SSH only from named administrator IP ranges. Do not allow PostgreSQL 5432, Redis, PHP-FPM, or any development port from the public internet.
5. If the database is remote, allow its port only from the app server's private address and require verified TLS. Do not route database traffic through a public, unrestricted interface.
6. Confirm client browsers can reach the currently referenced HTTPS asset hosts (`cdn.jsdelivr.net`, `cdnjs.cloudflare.com`, `fonts.googleapis.com`, and `unpkg.com`) and the OpenStreetMap tile service (`tile.openstreetmap.org`). The map will show markers but no usable basemap if its tile service is blocked. Review the tile provider's attribution and usage policy; procure an approved/self-hosted tile provider for material or high-volume operational use.
7. Verify the provider firewall and Linux host firewall still block PostgreSQL and remote administration from the public internet.

## 2. Patch the host and install runtime packages

1. Start from a supported Ubuntu LTS image. Apply OS updates, set the correct timezone and NTP, enable automatic security updates, and create a named non-root deployment account.

   ```bash
   sudo apt update
   sudo apt full-upgrade -y
   sudo adduser --disabled-password --gecos '' deploy
   sudo timedatectl set-timezone Africa/Harare
   ```

2. Install Nginx, PHP 8.3-FPM/CLI, the application extensions, certificate tooling, and PostgreSQL client utilities:

   ```bash
   sudo apt install -y nginx certbot python3-certbot-nginx ca-certificates curl unzip \
     php8.3-cli php8.3-fpm php8.3-common php8.3-curl php8.3-gd php8.3-mbstring \
     php8.3-pgsql php8.3-xml php8.3-zip postgresql-client
   ```

   Ubuntu 24.04's PHP 8.3 packages satisfy the project's PHP 8.2+ requirement. Confirm `ctype`, `fileinfo`, `dom`, `gd`, `mbstring`, `pdo_pgsql`, `pgsql`, `simplexml`, `xml`, `xmlreader`, `xmlwriter`, and `zip` are enabled. Configure PHP-FPM's `php.ini` with `cgi.fix_pathinfo=0`, the organisation's timezone, a suitable memory/upload limit, and OPcache; restart PHP-FPM after changes.
3. Install PostgreSQL 18 from the official PGDG repository if the database is local. These are the same repository steps used by the repository's CI workflow:

   ```bash
   sudo install -d /usr/share/postgresql-common/pgdg
   sudo curl --fail --output /usr/share/postgresql-common/pgdg/apt.postgresql.org.asc \
     https://www.postgresql.org/media/keys/ACCC4CF8.asc
   . /etc/os-release
   echo "deb [signed-by=/usr/share/postgresql-common/pgdg/apt.postgresql.org.asc] https://apt.postgresql.org/pub/repos/apt ${VERSION_CODENAME}-pgdg main" \
     | sudo tee /etc/apt/sources.list.d/pgdg.list
   sudo apt update
   sudo apt install -y postgresql-18
   ```

   For a managed/private PostgreSQL service, skip the local server package and install its CA certificate/configuration instead.
4. Install Composer 2 from its official distribution with checksum/signature verification. Install Node.js 20 LTS and npm from an approved repository, or build assets in CI using Node 20 and include `public/build` in the reviewed release artifact.
5. Verify runtime versions and PHP modules:

   ```bash
   php -v
   php -m
   php-fpm8.3 -v
   composer --version
   node --version
   npm --version
   psql --version
   ```

## 3. Create PostgreSQL roles and database

1. Connect as the PostgreSQL administrator (`sudo -u postgres psql`) and create a migration owner and restricted application runtime role. Set secrets interactively using `\password` so they are not placed in shell history:

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

2. Keep a local database listening on loopback/private interfaces only. For a managed database use its private endpoint, verified TLS CA, and network allowlist. Do not use the PostgreSQL superuser in application configuration.
3. Keep `RIDA_WMS_TEST` separate and disposable. The automated suite runs migrations and clears that database; never configure tests to point at `RIDA_WMS`.

## 4. Deploy the reviewed release

1. Place the reviewed source release at `/var/www/rida-wms/app`. The Nginx document root must be `/var/www/rida-wms/app/public`, never the project root.
2. Install PHP dependencies and build front-end assets from the project root:

   ```bash
   cd /var/www/rida-wms/app
   composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
   npm ci
   npm run build
   ```

   `node_modules` is not a web document root and must not be served. Retain the generated `public/build` release files.
3. Create `.env` from `.env.example` if this is a new installation. Set its owner to the deploy account and allow only the deploy account and PHP-FPM group to read it. Never commit `.env` or expose it under `public`.
4. Generate `APP_KEY` once for a new system only. Preserve the same key across deployments, backups, and restores; changing it can invalidate encrypted application data and sessions.
5. Configure `.env` for this single-server production instance:

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

   Substitute values from the approved secret store. For external PostgreSQL, use the provider's verified TLS settings (not `prefer`). File session/cache storage is for one server only. Multi-server deployments require shared session, cache, queue, and persistent file storage.

## 5. Migrate and create the first administrator

1. Back up any existing production database and uploaded/private files before migration. For a new installation, verify the target is the empty `RIDA_WMS` database.
2. Keep migration-role credentials active in `.env` while running:

   ```bash
   cd /var/www/rida-wms/app
   php artisan migrate --force
   ```

3. Before configuration caching, set unique `INITIAL_ADMIN_EMAIL` and `INITIAL_ADMIN_PASSWORD` values in the protected `.env`. Use a strong password (at least 12 characters); never set a shared/default admin credential.
4. Run the one-time bootstrap seeder and inspect its output:

   ```bash
   php artisan db:seed --class=UserSeeder --force
   ```

   Stop if it reports that no administrator was created. Resolve the email/password/configuration issue before proceeding.
5. Remove `INITIAL_ADMIN_EMAIL`, `INITIAL_ADMIN_PASSWORD`, and any `INITIAL_ADMIN_RESET_PASSWORD` flag from `.env`. Do not run demo seeders or production test fixtures.
6. Grant the runtime role access to existing tables and sequences, then replace migration credentials in `.env` with the runtime role:

   ```sql
   GRANT SELECT, INSERT, UPDATE, DELETE ON ALL TABLES IN SCHEMA public TO rwims_runtime;
   GRANT USAGE, SELECT, UPDATE ON ALL SEQUENCES IN SCHEMA public TO rwims_runtime;
   ```

   Keep migration credentials protected and use them only for planned schema changes. Do not use the runtime account to create schema objects.
7. Build Laravel's production caches after `.env` is final:

   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

## 6. Set ownership and configure Nginx/PHP-FPM

1. Give the deployment account ownership of code and the PHP-FPM group read access. Only Laravel's writable paths should be writable by the web process:

   ```bash
   sudo chown -R deploy:www-data /var/www/rida-wms/app
   sudo find /var/www/rida-wms/app -type d -exec chmod 750 {} \;
   sudo find /var/www/rida-wms/app -type f -exec chmod 640 {} \;
   sudo chmod -R ug+rwX /var/www/rida-wms/app/storage /var/www/rida-wms/app/bootstrap/cache
   sudo chmod 640 /var/www/rida-wms/app/.env
   ```

2. Create `/etc/nginx/sites-available/rida-wms` with the following site. Replace the hostname and confirm the PHP-FPM socket matches the installed PHP version:

   ```nginx
   server {
       listen 80;
       listen [::]:80;
       server_name wms.example.org;
       root /var/www/rida-wms/app/public;
       index index.php;
       client_max_body_size 20m;

       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }

       location ~ ^/index\.php(/|$) {
           fastcgi_pass unix:/run/php/php8.3-fpm.sock;
           fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
           include fastcgi_params;
           fastcgi_hide_header X-Powered-By;
       }

       location ~ \.php$ { return 404; }
       location ~ /\. { deny all; }
   }
   ```

3. Enable and validate the site, then reload services:

   ```bash
   sudo ln -s /etc/nginx/sites-available/rida-wms /etc/nginx/sites-enabled/rida-wms
   sudo nginx -t
   sudo systemctl enable --now nginx php8.3-fpm postgresql
   sudo systemctl reload nginx
   ```

   If the site link already exists, inspect it instead of creating another link. Verify no server block points at the project root.
4. If the application serves deliberately public uploads, create `php artisan storage:link` and verify the target. Keep private documents in the private local disk, not the public symlink.

## 7. Obtain TLS and verify public routing

1. Confirm public DNS resolves to the reserved address and port 80 reaches this Nginx host. Request a certificate for the DNS hostname and enable the HTTPS redirect:

   ```bash
   sudo certbot --nginx -d wms.example.org
   sudo certbot renew --dry-run
   ```

2. Confirm the certificate renew timer is enabled, HTTP redirects to HTTPS, and `https://wms.example.org/up` returns success from an outside network.
3. Verify the Laravel response includes security headers and HTTPS-only session cookies. Verify no public firewall allows PostgreSQL, Redis, SSH from all addresses, or developer ports.
4. Do not accept certificate warnings. A certificate issued for the DNS name will not validate a request made to the raw IP address.

## 8. Run the queue worker and scheduler

1. Create `/etc/systemd/system/rida-wms-queue.service`:

   ```ini
   [Unit]
   Description=RIDA RWIMS database queue worker
   After=network.target postgresql.service

   [Service]
   Type=simple
   User=www-data
   Group=www-data
   WorkingDirectory=/var/www/rida-wms/app
   ExecStart=/usr/bin/php artisan queue:work database --sleep=3 --tries=3 --timeout=60
   Restart=always
   RestartSec=5
   KillSignal=SIGTERM
   TimeoutStopSec=70

   [Install]
   WantedBy=multi-user.target
   ```

2. Start and verify the worker. `--timeout=60` is below the configured `retry_after=90`; keep that ordering if either value changes.

   ```bash
   sudo systemctl daemon-reload
   sudo systemctl enable --now rida-wms-queue
   sudo systemctl status rida-wms-queue
   ```

3. Add this line to the deployment account's crontab (`sudo -u deploy crontab -e`) to run Laravel's scheduler every minute:

   ```cron
   * * * * * cd /var/www/rida-wms/app && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
   ```

4. Verify queue logs, systemd status, failed jobs, and scheduler execution. Ensure the runtime account can write to Laravel storage and connect to the queue tables.

## 9. Configure backups and recovery

1. Schedule an encrypted PostgreSQL custom-format dump and a separate backup of persistent private/uploaded files. Store copies off-server with restricted access and documented retention. Preserve `APP_KEY` in a secure secret manager.
2. Use protected `.pgpass`/service credentials or a secrets manager for non-interactive `pg_dump`; do not put passwords in shell history or backup scripts. Include a timestamp and monitor exit status, file size, and offsite transfer.
3. Restore database and files into a separate staging instance. Verify login, role rules, five-record workflows, maps, PDFs, and file access. Measure restore duration and record the recovery point. Do not call the system recoverable until restore is tested.
4. Monitor disk space, PostgreSQL, Nginx, PHP-FPM, queue worker, certificate renewal, backups, error logs, and dependency advisories. Centralize alerts and define who responds.

## 10. Go-live and routine release order

1. Run `php artisan test`, `composer audit`, `composer validate --no-check-publish`, `npm audit`, `npm run build`, and `php artisan route:list` from the reviewed release, using only the separate test database.
2. In staging, enter at least five records in each capture workflow: boreholes, prospecting, dams/inspections, piped schemes/maintenance, irrigation/maintenance, water points, consumable catalog/suppliers, stock requests/issues/usage, tickets, clients, invoices, and payments. Test distinct map coordinates and province/yield filtering. Never insert fixtures in production.
3. Verify desktop and real-phone layouts, map markers/tiles/popups, guest denial, operator ownership, viewer read-only access, admin controls, PDFs/exports, mail, background queue, secure cookies, HTTPS redirect, health endpoint, and restore drill.
4. For a release: take and verify a backup, enter maintenance mode, publish reviewed code and built assets, run migrations with the migration role, restore the restricted runtime role, clear/rebuild caches, restart queue workers, smoke-test the system, then exit maintenance mode. Keep the preceding release and backup ready for rollback.
5. Keep the service in maintenance mode and do not announce launch if TLS, authorization, data integrity, map operation, backup restore, or RIDA business acceptance is incomplete.

## Public-IP limitation and business sign-off

The public IP must be reserved, routed, and allowed by the provider firewall. Use a DNS hostname pointing at it for trusted HTTPS; do not publish an HTTP-only IP URL. Confirm DNS ownership and certificate renewal with the network administrator. Geographic access policy, the cost-savings KPI assumptions, purchasing, and finance requirements listed in `README.md` still need RIDA approval before those capabilities are described as complete.