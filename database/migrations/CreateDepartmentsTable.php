<?php

namespace Database\Migrations;

require_once __DIR__ . '/../../app/Core/BaseMigration.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use App\Core\BaseMigration;

// ✅ مسیر جدید

class CreateDepartmentsTable
{
    public static function up(): void
    {
        Capsule::schema()->create(BaseMigration::tableName('departments'), function ($table) {
            BaseMigration::addPrimaryKey($table);

            $table->string('name')->unique();

            BaseMigration::addTimestampsAndSoftDeletes($table);
        });

        echo "✅ Table 'erp_departments' created successfully!\n";
    }

    public static function down(): void
    {
        Capsule::schema()->dropIfExists(BaseMigration::tableName('departments'));
        echo "❌ Table 'erp_departments' dropped successfully!\n";
    }
}
