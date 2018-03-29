<?php
/**
 * Created by PhpStorm.
 * User: marc.wilhelm
 * Date: 18.01.2018
 * Time: 14:25
 */

namespace App\Test\Controller;

use App\Controller\AppController;
use App\Test\ApiTestCase;

/**
 * Class IndexControllerTest
 * @coversDefaultClass App\Controller\IndexController
 */
class IndexControllerTest extends ApiTestCase
{
    /**
     * Test page found
     *
     * @covers ::indexAction
     * @covers AppController::render
     */
    public function testPageNotFound()
    {
        $request = $this->createRequest('GET', '/');
        $response = $this->request($request);
        $this->assertEquals(200, $response->getStatusCode());
        //$this->assertEmpty((string)$response->getBody());
        $this->assertContains('html', $response->getBody()->__toString());
    }

    /**
     * Test home page with language
     *
     * @covers ::indexAction
     * @covers AppController::render
     */
    public function testGetWithLang()
    {
        $request = $this->createRequest('GET', '/de');
        $response = $this->request($request);
        $this->assertEquals(200, $response->getStatusCode());
        //$this->assertEmpty((string)$response->getBody());
        $this->assertContains('html', $response->getBody()->__toString());
    }
}
