<?php

use App\Service\Game;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

require_once __DIR__ . '/../config/bootstrap.php';

try {
    $game = new Game();
} catch (ReflectionException $e) {
    // this should NEVER happen
}
$webSocket = new WsServer($game);
$httpServer = new HttpServer($webSocket);

$server = IoServer::factory($httpServer,8000);
$server->run();
