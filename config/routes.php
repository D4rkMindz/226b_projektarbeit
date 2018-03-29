<?php
$language = '{language:(?:de|en)}';

$app->get('/[' . $language . ']', 'App\Controller\IndexController:indexAction')->setName('root');
$app->get('/' . $language . '/errorpage', 'App\Controller\ErrorController:notFoundAction')->setName('notFound');
$app->get('/api', 'App\Controller\ApiController:indexAction')->setName('api');
$app->get('/api/home', 'App\Controller\ApiController:redirectToHomeAction')->setName('api.to-home');