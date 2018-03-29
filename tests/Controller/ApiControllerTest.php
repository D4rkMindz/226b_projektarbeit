<?php

namespace App\Test\Controller;

use App\Controller\AppController;
use App\Test\ApiTestCase;

/**
 * ApiControllerTest
 * @coversDefaultClass App\Controller\ApiController
 */
class ApiControllerTest extends ApiTestCase
{
    /**
     * Test JSON action
     *
     * @covers ::indexAction
     * @covers AppController::json
     * @return void
     */
    public function testIndexAction()
    {
        $request = $this->createRequest('GET', '/api');
        $response = $this->request($request);
        $this->assertEquals(200, $response->getStatusCode());
        $body = (string)$response->getBody();
        $this->assertContains('Hello World', $body);
    }

    /**
     * Test redirect action
     *
     * @covers ::redirectToHomeAction
     * @covers AppController::redirect
     * @return void
     */
    public function testRedirectToHomeAction()
    {
        $request = $this->createRequest('GET', '/api/home');
        $response = $this->request($request);
        $this->assertEquals(301, $response->getStatusCode());
        $body = (string)$response->getBody();
        $this->assertEmpty($body);
    }
}
