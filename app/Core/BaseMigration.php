<?php

namespace App\Core;

use Illuminate\Database\Schema\Blueprint;

class BaseMigration
{
    public static function tableName(string $name): string
    {
        return $name;
    }

    public static function addPrimaryKey($table): void
    {
        $table->id();
    }


    public static function addTimestampsAndSoftDeletes($table): void
    {
        $table->timestamps();
        $table->softDeletes();
    }
}
