<?php

namespace App\Test\Controller;

use App\Test\ApiTestCase;

/**
 * Class ErrorControllerTest.
 *
 * @coversDefaultClass \App\Controller\ErrorController
 */
class ErrorControllerTest extends ApiTestCase
{
    /**
     * Test notFoundAction.
     */
    public function testNotFoundAction()
    {
        $request = $this->createRequest('GET', '/de/errorpage');
        $response = $this->request($request);
        $this->assertEquals(200, $response->getStatusCode());
        $body = (string) $response->getBody();
        $this->assertContains('404 - Page not Found', $body);
    }
}
