<?php

namespace App\Test\Controller;

use App\Test\ApiTestCase;
use Exception;
use Slim\Exception\MethodNotAllowedException;
use Slim\Exception\NotFoundException;

/**
 * Class ErrorControllerTest
 * @coversDefaultClass App\Controller\ErrorController
 */
class ErrorControllerTest extends ApiTestCase
{
    /**
     * Test notFoundAction
     *
     * @covers ::notFoundAction
     * @throws Exception
     * @throws MethodNotAllowedException
     * @throws NotFoundException
     */
    public function testNotFoundAction()
    {
        $request = $this->createRequest('GET', '/de/errorpage');
        $response = $this->request($request);
        $this->assertEquals(200, $response->getStatusCode());
        $body = (string)$response->getBody();
        $this->assertContains('404 - Page not Found', $body);
    }
}
