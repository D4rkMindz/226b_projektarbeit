<?php
$language = '{language:(?:de|en)}'; // opt. later

$app->get('/', 'App\Controller\IndexController:indexAction')->setName('root');
$app->get('/errorpage', 'App\Controller\ErrorController:notFoundAction')->setName('notFound');
$app->get('/game', 'App\Controller\GameController:indexAction')->setName('game');
$app->get('/game/{game_id}', 'App\Controller\GameController:indexAction')->setName('game.join');