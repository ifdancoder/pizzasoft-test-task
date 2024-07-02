<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

require_once __DIR__ . '/../vendor/autoload.php';

$capsule = new Capsule;

$config = include __DIR__ . "/../config.php";

$capsule->addConnection([
    'driver' => 'mysql',
    'host' => $config['mysql_host'],
    'database' => $config['mysql_database'],
    'username' => $config['mysql_username'],
    'password' => $config['mysql_password'],
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
], 'default');

$capsule->setEventDispatcher(new Dispatcher(new Container));
$capsule->setAsGlobal();
$capsule->bootEloquent();

return $capsule;