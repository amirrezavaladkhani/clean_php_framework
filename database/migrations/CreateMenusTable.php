<?php

namespace Database\Migrations;

require_once __DIR__ . '/../../app/Core/BaseMigration.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use App\Core\BaseMigration; // ✅ مسیر جدید

class CreateMenusTable
{
    public static function up(): void
    {
        Capsule::schema()->create(BaseMigration::tableName('menus'), function ($table) {
            BaseMigration::addPrimaryKey($table);
            
            $table->string('title');
            $table->string('url')->nullable();
            $table->foreignId('department_id')
                ->constrained(BaseMigration::tableName('departments'))
                ->onDelete('cascade');
            $table->json('submenus')->nullable();

            BaseMigration::addTimestampsAndSoftDeletes($table);
        });

        echo "✅ Table 'erp_menus' created successfully!\n";
    }

    public static function down(): void
    {
        Capsule::schema()->dropIfExists(BaseMigration::tableName('menus'));
        echo "❌ Table 'erp_menus' dropped successfully!\n";
    }
}
