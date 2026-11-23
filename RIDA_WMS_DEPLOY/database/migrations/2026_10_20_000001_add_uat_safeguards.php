<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['users' => 'role', 'water_points' => 'functionality', 'piped_water_schemes' => 'functionality', 'irrigation_schemes' => 'functionality'] as $table => $column) {
            if (DB::getDriverName() === 'pgsql') {
                DB::statement("ALTER TABLE {$table} DROP CONSTRAINT IF EXISTS {$table}_{$column}_check");
            }
            Schema::table($table, fn (Blueprint $blueprint) => $blueprint->string($column)->change());
            if ($column === 'functionality') {
                DB::table($table)->whereIn($column, ['Nonfunctional', 'Non functional'])->update([$column => 'Non Functional']);
            }
            $allowed = $column === 'role'
                ? "'admin','operator','viewer','supervisor','finance','stores'"
                : "'Functional','Partially Functional','Non Functional'";
            if (DB::getDriverName() === 'pgsql') {
                DB::statement("ALTER TABLE {$table} ADD CONSTRAINT {$table}_{$column}_check CHECK ({$column} IN ({$allowed}))");
            }
        }

        Schema::table('users', fn (Blueprint $table) => $table->timestamp('last_seen_at')->nullable()->index());
        Schema::table('prospecting_sites', function (Blueprint $table) {
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('drilled_at')->nullable();
            $table->foreignId('drilled_by')->nullable()->constrained('users');
        });
        DB::table('prospecting_sites')->update(['submitted_at' => DB::raw('created_at')]);

        foreach (['water_points', 'piped_water_schemes', 'irrigation_schemes'] as $name) {
            Schema::table($name, function (Blueprint $table) {
                $table->string('province')->nullable()->index();
                $table->string('district')->nullable()->index();
            });
        }
        Schema::table('water_points', function (Blueprint $table) {
            $table->foreignId('borehole_id')->nullable()->unique()->constrained('boreholes')->restrictOnDelete();
        });
        Schema::table('dams', fn (Blueprint $table) => $table->string('dam_code')->nullable()->unique());
        foreach (DB::table('dams')->orderBy('id')->get(['id']) as $dam) {
            DB::table('dams')->where('id', $dam->id)->update(['dam_code' => 'DAM-' . str_pad((string) $dam->id, 4, '0', STR_PAD_LEFT)]);
        }
        foreach (['suppliers', 'consumable_categories'] as $name) {
            Schema::table($name, fn (Blueprint $table) => $table->softDeletes());
        }
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->string('action');
            $table->string('record_type');
            $table->unsignedBigInteger('record_id');
            $table->json('details')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['record_type', 'record_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        foreach (['suppliers', 'consumable_categories'] as $name) {
            Schema::table($name, fn (Blueprint $table) => $table->dropSoftDeletes());
        }
        Schema::table('water_points', function (Blueprint $table) {
            $table->dropConstrainedForeignId('borehole_id');
        });
        foreach (['water_points', 'piped_water_schemes', 'irrigation_schemes'] as $name) {
            Schema::table($name, fn (Blueprint $table) => $table->dropColumn(['province', 'district']));
        }
        Schema::table('dams', function (Blueprint $table) {
            $table->dropUnique(['dam_code']);
            $table->dropColumn('dam_code');
        });
        Schema::table('prospecting_sites', function (Blueprint $table) {
            $table->dropConstrainedForeignId('drilled_by');
            $table->dropColumn(['submitted_at', 'drilled_at']);
        });
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('last_seen_at'));
        // Keep the expanded role/status vocabulary: rolling it back would invalidate existing records.
    }
};
