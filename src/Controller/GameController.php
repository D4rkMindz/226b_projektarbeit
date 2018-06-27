<?php


namespace App\Controller;

use App\WebSocket\ActionHandler;
use App\WebSocket\ObserverableInterface;
use Mailgun\Model\Route\Action;
use Psr\Http\Message\ResponseInterface;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;


/**
 * Class GameController
 */
class GameController extends AppController
{
    private $config;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->config = $container->get('settings');
    }

    /**
     * Index action.
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return ResponseInterface
     */
    public function indexAction(Request $request, Response $response, array $args): ResponseInterface
    {
        $gameId = null;
        if (array_key_exists('game_id', $args)) {
            $gameId = $args['game_id'];
        }
        $data = [
            'gameId' => $gameId,
            'fieldSize' => $this->config['game']['fieldSize'],
            'ships' => $this->config['game']['ships']
        ];
        return $this->render($response, $request, 'Game/game.twig', $data);
    }
}