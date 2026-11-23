<?php

namespace App\Console\Commands;

use App\Services\UatDataRepair;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;

class PrepareUat extends Command
{
    protected $signature = 'uat:prepare {--apply : Back up, migrate, archive obvious test accounts and repair known samples} {--keep-admin=admin@rida.gov.zw}';
    protected $description = 'Prepare local/UAT data without resetting the database or removing record history';

    public function handle(): int
    {
        if (app()->environment('production')) {
            $this->error('This command is restricted to non-production UAT environments.');
            return self::FAILURE;
        }
        if (!$this->option('apply')) {
            $this->info('Dry run: no changes made. --apply creates a private database backup before migrations, archives obvious faker/test/local duplicate accounts, repairs known sample assets, and adds UAT scenarios. Other records are preserved.');
            return self::SUCCESS;
        }
        $keeper = \App\Models\User::where('email', $this->option('keep-admin'))->where('role', 'admin')->where('is_active', true)->first();
        if (!$keeper || !str_ends_with(strtolower($keeper->email), '@rida.gov.zw')) {
            $this->error('Choose an existing active @rida.gov.zw administrator with --keep-admin.');
            return self::FAILURE;
        }

        $directory = storage_path('app/private/uat-backups');
        if (!is_dir($directory) && !mkdir($directory, 0700, true)) {
            throw new \RuntimeException('Unable to create the private backup directory.');
        }
        $backup = $directory . DIRECTORY_SEPARATOR . 'before-uat-' . now()->format('Ymd-His') . '-' . bin2hex(random_bytes(3));
        $connection = config('database.connections.' . DB::getDefaultConnection());
        if (DB::getDriverName() === 'pgsql') {
            $backup .= '.dump';
            $process = new Process([
                env('PG_DUMP_PATH', 'pg_dump'), '--no-password', '--format=custom',
                '--host=' . $connection['host'], '--port=' . $connection['port'],
                '--username=' . $connection['username'], '--dbname=' . $connection['database'], '--file=' . $backup,
            ], base_path(), ['PGPASSWORD' => $connection['password']]);
            $process->setTimeout(180)->mustRun();
        } elseif (DB::getDriverName() === 'sqlite') {
            $backup .= '.sqlite';
            DB::connection()->getPdo()->exec('VACUUM INTO ' . DB::connection()->getPdo()->quote($backup));
        } else {
            $this->error('Automatic backup supports PostgreSQL and SQLite only. No changes made.');
            return self::FAILURE;
        }
        if (!is_file($backup) || filesize($backup) === 0) {
            throw new \RuntimeException('Backup was not verified; no migration or cleanup was started.');
        }
        $this->info('Verified private backup: ' . $backup);
        if (Artisan::call('migrate', ['--force' => true]) !== 0) {
            $this->error(Artisan::output());
            return self::FAILURE;
        }
        $this->line(Artisan::output());
        $counts = DB::transaction(function () {
            app(\Database\Seeders\DemoUserSeeder::class)->run();
            $counts = app(UatDataRepair::class)->run($this->option('keep-admin'));
            app(\Database\Seeders\UatScenarioSeeder::class)->run();
            return $counts;
        });
        foreach ($counts as $label => $count) {
            $this->info(str_replace('_', ' ', $label) . ': ' . $count);
        }
        \App\Services\ReportCache::flush();
        $this->warn('Sample coordinates and costs are UAT examples, not verified field measurements or accounting records. Review real invalid records before approving them.');
        return self::SUCCESS;
    }
}
