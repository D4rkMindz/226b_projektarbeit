<?php

use App\WebSocket\ActionHandler;
use App\WebSocket\Game;
use App\WebSocket\Observer;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

require_once __DIR__ . '/../config/bootstrap.php';

// Keep script running
// https://stackoverflow.com/questions/9798438/automatically-restart-php-script-on-exit
if (function_exists('pcntl_exec')) {
    echo ++$argv[1];
    $_ = $_SERVER['_'];
    register_shutdown_function(function () {
        global $_, $argv;
        pcntl_exec($_, $argv);
    });
}

$actionhandler = new ActionHandler();
$observer = new Observer();
$observer->attach($actionhandler);

try {
    $game = new Game($observer);
} catch (ReflectionException $e) {
    // this should NEVER happen
}
$webSocket = new WsServer($game);
$httpServer = new HttpServer($webSocket);

$server = IoServer::factory($httpServer,8000);
$server->run();
