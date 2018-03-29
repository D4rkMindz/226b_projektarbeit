<?php

namespace App\Controller;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class ErrorController.
 */
class ErrorController extends AppController
{
    /**
     * Not found action.
     *
     * @param Request $request
     * @param Response $response
     *
     * @return ResponseInterface
     */
    public function notFoundAction(Request $request, Response $response): ResponseInterface
    {
        return $this->render($response, $request, 'Error/error.twig');
    }
}
