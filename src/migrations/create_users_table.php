<?php

require_once __DIR__ . '/../../bootstrap.php';

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->create('test_table', function ($table) {
    $table->increments('id');
    $table->string('name');
    $table->timestamps();
});

echo "Migration created successfully!";
