<?php

namespace App\Test;

use Aura\Session\Session;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use ReflectionException;
use Slim\App;
use Slim\Container;
use Slim\Exception\MethodNotAllowedException;
use Slim\Exception\NotFoundException;
use Slim\Http\Environment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\RequestBody;
use Slim\Http\Response;
use Slim\Http\UploadedFile;
use Slim\Http\Uri;

/**
 * Class ApiTestCase.
 */
abstract class ApiTestCase extends BaseTestCase
{
    /**
     * @var App|null
     */
    protected $app;

    /**
     * Set up method.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function setUp()
    {
        $this->app = require __DIR__ . '/../config/bootstrap.php';

        /**
         * @var Session
         */
        $session = $this->app->getContainer()->get(Session::class);
        $session->clear();
    }

    /**
     * Tear down method.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function tearDown()
    {
        /**
         * @var Session
         */
        $session = $this->app->getContainer()->get(Session::class);
        $session->destroy();
        $this->app = null;
    }

    /**
     * Create mock request.
     *
     * @param string $method
     * @param string $url
     *
     * @return Request
     */
    protected function createRequest(string $method, string $url): Request
    {
        $env = Environment::mock();
        $uri = Uri::createFromString($url);
        $headers = Headers::createFromEnvironment($env);
        $cookies = [];
        $serverParams = $env->all();
        $body = new RequestBody();
        $uploadedFiles = UploadedFile::createFromEnvironment($env);

        $request = new Request($method, $uri, $headers, $cookies, $serverParams, $body, $uploadedFiles);

        return $request;
    }

    /**
     * Make silent request.
     *
     * @param Request $request
     *
     * @throws Exception
     * @throws MethodNotAllowedException
     * @throws NotFoundException
     *
     * @return Response
     */
    protected function request(Request $request): Response
    {
        $container = $this->getContainer();
        $this->setContainer($container, 'request', $request);
        $this->setContainer($container, 'response', new Response());
        $response = $this->app->run(true);

        return $response;
    }

    /**
     * Set container.
     *
     * @param Container $container
     * @param string $key
     * @param mixed $value
     *
     * @throws ReflectionException
     */
    protected function setContainer(Container $container, string $key, $value)
    {
        $class = new ReflectionClass(\Pimple\Container::class);
        $property = $class->getProperty('frozen');
        $property->setAccessible(true);
        $values = $property->getValue($container);
        unset($values[$key]);
        $property->setValue($container, $values);
        $container[$key] = $value;
    }

    /**
     * Get container object for tests.
     *
     * @return Container
     */
    protected function getContainer(): Container
    {
        $container = $this->app->getContainer();

        return $container;
    }

    /**
     * Add post data to request.
     *
     * @param Request $request
     * @param array $data
     *
     * @return Request
     */
    protected function withFormData(Request $request, array $data): Request
    {
        if ($request->getMethod() == 'GET') {
            $request = $request->withMethod('POST');
        }

        if (!empty($data)) {
            $request = $request->withParsedBody($data);
        }
        $request = $request->withHeader('Content-Type', 'application/x-www-form-urlencoded');

        return $request;
    }

    /**
     * Add JSON body to request.
     *
     * @param Request $request
     * @param array $data
     *
     * @return Request
     */
    protected function withJson(Request $request, array $data): Request
    {
        $body = $request->getBody();
        $body->write(json_encode($data));
        $request = $request->withHeader('Content-Type', 'application/json');

        return $request->withBody($body);
    }
}
