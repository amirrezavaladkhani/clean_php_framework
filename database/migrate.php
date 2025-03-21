<?php

define('IS_CLI', php_sapi_name() === 'cli');

require_once __DIR__ . '/../bootstrap.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Filesystem\Filesystem;

$migrationPath = __DIR__ . DIRECTORY_SEPARATOR . 'migrations';

$filesystem = new Filesystem();

if (!$filesystem->exists($migrationPath)) {
    echo "❌ Migration directory not found: $migrationPath\n";
    exit(1);
}

$migrations = $filesystem->files($migrationPath);

$action = $argv[1] ?? 'help';

if (!in_array($action, ['up', 'down', 'status']) || $action === 'help') {
    echo "❌ Invalid command! Use:\n";
    echo "   php database/migrate.php up      → Run all migrations\n";
    echo "   php database/migrate.php down    → Rollback all migrations\n";
    echo "   php database/migrate.php status  → Show applied migrations\n";
    exit(1);
}

try {
    if (!Capsule::schema()->hasTable('migrations')) {
        Capsule::schema()->create('migrations', function ($table) {
            $table->id();
            $table->string('migration');
            $table->timestamps();
        });
        echo "✅ Table 'migrations' created successfully!\n";
    }
} catch (PDOException $e) {
    echo "❌ Database connection error: " . $e->getMessage() . "\n";
    exit(1);
}

// Status Command
if ($action === 'status') {
    try {
        $appliedMigrations = Capsule::table('migrations')->pluck('migration')->toArray();
    } catch (PDOException $e) {
        echo "❌ Cannot fetch migration status: " . $e->getMessage() . "\n";
        exit(1);
    }

    if (empty($appliedMigrations)) {
        echo "🔹 No migrations applied yet.\n";
    } else {
        echo "✅ Applied migrations:\n";
        foreach ($appliedMigrations as $migration) {
            echo "   - $migration\n";
        }
    }
    exit(0);
}

foreach ($migrations as $migration) {
    require_once $migration;
    $className = pathinfo($migration, PATHINFO_FILENAME);

    if (!class_exists($className)) {
        echo "❌ Migration class '$className' not found in $migration\n";
        continue;
    }

    try {
        if ($action === 'up') {
            if (Capsule::table('migrations')->where('migration', $className)->exists()) {
                echo "⚠️ Migration '$className' already applied. Skipping...\n";
                continue;
            }
            
            $className::up();
            Capsule::table('migrations')->insert(['migration' => $className, 'created_at' => now()]);
            echo "✅ Migration '$className' executed successfully!\n";
        } elseif ($action === 'down') {
            if (!Capsule::table('migrations')->where('migration', $className)->exists()) {
                echo "⚠️ Migration '$className' was not applied. Skipping rollback...\n";
                continue;
            }
            $className::down();
            Capsule::table('migrations')->where('migration', $className)->delete();
            echo "❌ Migration '$className' rolled back successfully!\n";
        }
    } catch (PDOException $e) {
        echo "❌ Database error in migration '$className': " . $e->getMessage() . "\n";
    } catch (Exception $e) {
        echo "❌ Error in migration '$className': " . $e->getMessage() . "\n";
    }
}

echo "✅ Migration process completed!\n";
