<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use RuntimeException;

class ImportSqliteData extends Command
{
    protected $signature = 'app:import-sqlite-data
        {--source= : SQLite source path (defaults to database/database.sqlite)}
        {--dry-run : Show row counts only; do not write data}
        {--force : Import into empty PostgreSQL tables}';

    protected $description = 'Copy existing application records from SQLite into an empty PostgreSQL database.';

    private array $tableOrder = [
        'users',
        'dams',
        'piped_water_schemes',
        'irrigation_schemes',
        'water_points',
        'prospecting_sites',
        'boreholes',
        'geology_layers',
        'casing_details',
        'borehole_consumable_usages',
        'borehole_bit_size_intervals',
        'dam_maintenance',
        'piped_water_scheme_maintenances',
        'scheme_maintenances',
        'irrigation_maintenances',
        'consumable_categories',
        'suppliers',
        'consumables',
        'inventory',
        'inventory_movements',
        'stock_requests',
        'stock_request_items',
        'usage_reports',
        'usage_report_items',
        'purchase_orders',
        'purchase_order_items',
        'clients',
        'invoices',
        'invoice_items',
        'invoice_payments',
        'support_tickets',
    ];

    private array $sensitiveTables = [
        'sessions',
        'password_reset_tokens',
        'personal_access_tokens',
    ];

    public function handle(): int
    {
        $sourcePath = $this->option('source') ?: database_path('database.sqlite');
        if (!is_file($sourcePath)) {
            $this->error("SQLite source not found: {$sourcePath}");
            return self::FAILURE;
        }

        if (!$this->option('dry-run') && !$this->option('force')) {
            $this->error('Run with --dry-run first. Use --force only after reviewing the counts.');
            return self::FAILURE;
        }

        config(['database.connections.sqlite.database' => $sourcePath]);
        DB::purge('sqlite');

        try {
            $source = DB::connection('sqlite');
            $target = DB::connection('pgsql');
            $sourceTables = array_flip(array_map(
                fn ($table) => Str::afterLast($table, '.'),
                $source->getSchemaBuilder()->getTableListing()
            ));
            $counts = [];

            foreach ($this->tableOrder as $table) {
                if (!isset($sourceTables[$table])) {
                    continue;
                }

                $sourceCount = $source->table($table)->count();
                if ($sourceCount === 0) {
                    continue;
                }

                if (!Schema::connection('pgsql')->hasTable($table)) {
                    throw new RuntimeException("PostgreSQL table is missing: {$table}. Run migrations first.");
                }

                $targetCount = $target->table($table)->count();
                if ($targetCount !== 0) {
                    throw new RuntimeException("Refusing to merge into non-empty PostgreSQL table {$table} ({$targetCount} rows).");
                }

                $counts[$table] = $sourceCount;
            }

            $this->table(
                ['SQLite source table', 'Rows to import'],
                collect($counts)->map(fn ($count, $table) => [$table, $count])->values()->all()
            );
            $this->line('Authentication sessions, password-reset tokens, and API tokens: skipped.');

            if ($this->option('dry-run')) {
                $this->info('Dry run only; no data was written.');
                return self::SUCCESS;
            }

            $target->transaction(function () use ($source, $target, $counts): void {
                foreach ($this->tableOrder as $table) {
                    if (!isset($counts[$table]) || $counts[$table] === 0) {
                        continue;
                    }

                    $sourceColumns = $source->getSchemaBuilder()->getColumnListing($table);
                    $targetColumns = $target->getSchemaBuilder()->getColumnListing($table);
                    $columns = array_values(array_intersect($sourceColumns, $targetColumns));
                    $rows = $source->table($table)->orderBy('id')->get($columns);

                    foreach ($rows->chunk(250) as $chunk) {
                        $records = $chunk->map(function ($row) use ($table) {
                            $record = (array) $row;
                            if ($table === 'users' && array_key_exists('remember_token', $record)) {
                                $record['remember_token'] = null;
                            }
                            return $record;
                        })->all();
                        $target->table($table)->insert($records);
                    }

                    $sequence = $target->selectOne("SELECT pg_get_serial_sequence(?, 'id') AS sequence", [$table])->sequence;
                    if ($sequence) {
                        $target->selectOne(
                            "SELECT setval(?, COALESCE((SELECT MAX(id) FROM \"{$table}\"), 1), EXISTS (SELECT 1 FROM \"{$table}\"))",
                            [$sequence]
                        );
                    }
                }
            });

            $this->info('SQLite records copied successfully. Sessions, password resets, and API tokens were intentionally not copied.');
            return self::SUCCESS;
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());
            return self::FAILURE;
        }
    }
}
