<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        }

        if (!Schema::hasColumn($tableNames['permissions'], 'guard_name')) {
            Schema::table($tableNames['permissions'], function (Blueprint $table) {
                $table->string('guard_name', 255)->after('name')->default(config('auth.defaults.guard'));
            });
        }

        if (!Schema::hasColumn($tableNames['roles'], 'guard_name')) {
            Schema::table($tableNames['roles'], function (Blueprint $table) {
                $table->string('guard_name', 255)->after('name')->default(config('auth.defaults.guard'));
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableNames = config('permission.table_names');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        }

        // The 'guard_name' column is created in a previous migration, so we should not drop it here.
        // if (Schema::hasColumn($tableNames['permissions'], 'guard_name')) {
        //     Schema::table($tableNames['permissions'], function (Blueprint $table) {
        //         $table->dropColumn('guard_name');
        //     });
        // }

        // if (Schema::hasColumn($tableNames['roles'], 'guard_name')) {
        //     Schema::table($tableNames['roles'], function (Blueprint $table) {
        //         $table->dropColumn('guard_name');
        //     });
        // }
    }
};