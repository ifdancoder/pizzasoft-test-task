<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Database\Migrations\Migrator;
use Symfony\Component\Console\Output\ConsoleOutput;

$capsule = require __DIR__ . '/bootstrap.php';

$migrationsPath = __DIR__ . '/migrations';

if (!Capsule::schema()->hasTable('migrations')) {
    Capsule::schema()->create('migrations', function (Blueprint $table) {
        $table->increments('id');
        $table->string('migration');
        $table->integer('batch');
    });
}

$files = (new Filesystem)->glob($migrationsPath . '/*.php');
sort($files);

$repository = new DatabaseMigrationRepository($capsule->getDatabaseManager(), 'migrations');

if (!$repository->repositoryExists()) {
    $repository->createRepository();
}

$migrator = new Migrator($repository, $capsule->getDatabaseManager(), new Filesystem);
$migrator->setOutput(new ConsoleOutput());

$old_ran = $repository->getRan();

$migrator->run($migrationsPath);

if ($migrator->repositoryExists()) {
    $ran = $repository->getRan();
    $migrations = [];
    foreach ($ran as $file) {
        if (!in_array($file, $old_ran)) {
            $migrations[] = $file;
        }
    }
    if (count($migrations) > 0) {
        echo "Migrations to run:\n";
        foreach ($migrations as $migration) {
            echo "  - $migration\n";
        }
    } else {
        echo "No migrations to run.\n";
    }
} else {
    echo "Migrations table not found.\n";
}
