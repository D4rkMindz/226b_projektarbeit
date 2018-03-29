<?php

namespace App\Controller;

use Psr\Http\Message\ResponseInterface;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

/**
 * Class ApiController
 */
class ApiController extends AppController
{
    /**
     * @var Router
     */
    protected $router;

    /**
     * ApiController constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->router = $container->get('router');
    }

    /**
     * Index action
     *
     * @param Request $request
     * @param Response $response
     * @return ResponseInterface
     */
    public function indexAction(Request $request, Response $response): ResponseInterface
    {
        return $this->json($response, ['message'=> 'Hello World']);
    }

    /**
     * Redirect to home.
     *
     * @param Request $request
     * @param Response $response
     * @return ResponseInterface
     */
    public function redirectToHomeAction(Request $request, Response $response): ResponseInterface
    {
        return $this->redirect($response, $this->router->pathFor('root'));
    }
}
