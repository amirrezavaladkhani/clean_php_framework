<?php

namespace database;

use Illuminate\Database\Schema\Blueprint;

class BaseMigration
{
    public static function tableName($name): string
    {
        return config('database.connections.mysql.prefix') . $name;
    }

    public static function addCommonFields(Blueprint $table): void
    {
        $table->id();
        $table->timestamps();
        $table->softDeletes();
    }
}
