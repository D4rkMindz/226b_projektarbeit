<?php

use Cake\Database\Connection;
use Slim\App;

/** @var App $app */
$app = require __DIR__ . '/bootstrap.php';

$container = $app->getContainer();
$pdo = $container->get(Connection::class)->getDriver()->connection();

return array(
    'paths' => array(
        'migrations' => $container->get('settings')->get('migrations'),
    ),
    'environments' => array(
        'default_database' => 'local',
        'local' => array(
            'name' => $container->get('settings')->get('db')['database'],
            'connection' => $pdo,
        ),
    ),
);

