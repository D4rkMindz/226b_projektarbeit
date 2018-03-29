<?php

use App\Model\UserModel;
use App\Service\Login\LoginService;
use App\Service\Mail\MailerInterface;
use App\Service\Mail\MailgunAdapter;
use Aura\Session\Session;
use Aura\Session\SessionFactory;
use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;
use Interop\Container\Exception\ContainerException;
use Monolog\Logger;
use Odan\Twig\TwigAssetsExtension;
use Odan\Twig\TwigTranslationExtension;
use Slim\Container;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;
use Symfony\Component\Translation\Loader\MoFileLoader;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Translator;

$container = $app->getContainer();

/**
 * Environment container (for routes).
 *
 * @return Environment
 */
$container['environment'] = function (): Environment {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $_SERVER['SCRIPT_NAME'] = dirname(dirname($scriptName)) . '/' . basename($scriptName);

    return new Slim\Http\Environment($_SERVER);
};

/**
 * Twig container.
 *
 * @param Container $container
 * @return Twig
 * @throws ContainerException
 */
$container[Twig::class] = function (Container $container): Twig {
    $twigSettings = $container->get('settings')->get('twig');

    $basePath = rtrim(str_ireplace('index.php', '', $container->get('request')->getUri()->getBasePath()), '/');

    $twig = new Twig($twigSettings['viewPath'],
        ['cache' => $twigSettings['cachePath'], 'auto_reload' => $twigSettings['autoReload']]);
    $twig->addExtension(new TwigTranslationExtension());
    $twig->addExtension(new \Slim\Views\TwigExtension($container->get('router'), $basePath));
    $twig->addExtension(new TwigAssetsExtension($twig->getEnvironment(), $twigSettings['assetCache']));

    return $twig;
};

/**
 * Translator container.
 *
 * @param Container $container
 * @return Translator $translator
 * @throws ContainerException
 */
$container[Translator::class] = function (Container $container): Translator {
    $settings = $container->get('settings')->get(Translator::class);
    $translator = new Translator($settings['locale'], /** @scrutinizer ignore-type */
        new MessageSelector());
    $translator->addLoader('mo', new MoFileLoader());

    return $translator;
};

/**
 * Database connection container.
 *
 * @param Container $container
 * @return Connection
 * @throws ContainerException
 */
$container[Connection::class] = function (Container $container): Connection {
    $config = $container->get('settings')->get('db');
    $driver = new Mysql([
        'host' => $config['host'],
        'port' => $config['port'],
        'database' => $config['database'],
        'username' => $config['username'],
        'password' => $config['password'],
        'encoding' => $config['encoding'],
        'charset' => $config['charset'],
        'collation' => $config['collation'],
        'prefix' => '',
        'flags' => [
            // Enable exceptions
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            // Set default fetch mode
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_PERSISTENT => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8 COLLATE utf8_unicode_ci",
        ],
    ]);
    $db = new Connection([
        'driver' => $driver,
    ]);

    $db->connect();

    return $db;
};

/**
 * Session container.
 *
 * @param Container $container
 * @return Session
 * @throws ContainerException
 */
$container[Session::class] = function (Container $container): Session {
    $factory = new SessionFactory();
    $cookies = $container->get('request')->getCookieParams();
    $session = $factory->newInstance($cookies);
    $settings = $container->get('settings')->get(Session::class);
    $session->setName($settings['name']);
    $session->setCacheExpire($settings['cache_expire']);

    return $session;
};

/**
 * Logger container.
 *
 * @param Container $container
 * @return Logger
 * @throws ContainerException
 */
$container[Monolog\Logger::class] = function (Container $container) {
    return new Logger($container->get('settings')->get('logger')['main']);
};

/**
 * Not found handler.
 *
 * @param Container $container
 * @return Closure
 */
$container['notFoundHandler'] = function (Container $container) {
    return function (Request $request, Response $response) use ($container) {
        return $response->withRedirect($container->get('router')->pathFor('notFound', ['language' => 'en']));
    };
};
