<?php


namespace App\Service;


use Ratchet\ConnectionInterface;
use ReflectionException;
use SplObjectStorage;

class Game extends AbstractSocket
{
    const FIELD_SIZE = 13;

    /**
     * @var SplObjectStorage
     */
    private $clients;

    /**
     * Game constructor.
     * @throws ReflectionException
     */
    public function __construct()
    {
        parent::__construct();
        $this->clients = new SplObjectStorage();
    }

    /**
     * When a new connection is opened it will be passed to this method
     * @param  ConnectionInterface $conn The socket/connection that just connected to your application
     * @throws \Exception
     */
    function onOpen(ConnectionInterface $conn)
    {
        echo $conn->resourceId . " joined\n";
        // TODO: Implement onOpen() method.
    }

    /**
     * This is called before or after a socket is closed (depends on how it's closed).  SendMessage to $conn will not result in an error if it has already been closed.
     * @param  ConnectionInterface $conn The socket/connection that is closing/closed
     * @throws \Exception
     */
    function onClose(ConnectionInterface $conn)
    {
        // TODO: Implement onClose() method.
    }

    /**
     * If there is an error with one of the sockets, or somewhere in the application where an Exception is thrown,
     * the Exception is sent back down the stack, handled by the Server and bubbled back up the application through this method
     * @param  ConnectionInterface $conn
     * @param  \Exception $e
     * @throws \Exception
     */
    function onError(ConnectionInterface $conn, \Exception $e)
    {
        // TODO: Implement onError() method.
    }

    /**
     * Triggered when a client sends data through the socket
     * @param  \Ratchet\ConnectionInterface $from The socket/connection that sent the message to your application
     * @param  string $msg The message received
     * @throws \Exception
     */
    function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);
        if (!$this->isValidAction($data['type'])) {
            echo "{$from->resourceId} used invalid action: {$data['type']}\n";
        }

        $roomId = $this->getRoomByClientId($from->resourceId);
        if (empty($roomId) && ($data['type'] !== Game::ACTION_HOST && $data['type'] !== Game::ACTION_JOIN)) {
            $this->emitError($from, 'Please join a session');
            return;
        }

        $this->handleAction($data['type'], $data, $roomId, $from);
    }

    /**
     * Handle action
     *
     * @param string $action
     * @param array $data
     * @param string $roomId
     * @param ConnectionInterface $connection
     */
    private function handleAction(string $action, array $data, string $roomId, ConnectionInterface $connection)
    {
        switch ($action) {
            case self::ACTION_SHOT:
                $this->emitShot($roomId, $connection->resourceId, $data['x'], $data['y']);
                break;
            case self::ACTION_HOST:
                $roomId = $this->addClientAsHost($connection, $data['username']);
                $this->emitHost($roomId, $connection->resourceId, $roomId);
                break;
            case self::ACTION_JOIN:
                $this->addClientToRoom($connection, $data['room_id'], $data['username']);
                break;
            case self::ACTION_PLACE_SHIP:
                $this->addShip($roomId, $connection->resourceId, $data['startX'], $data['startY'], $data['endX'], $data['endY']);
                break;
            default:
                $this->emitError($connection);
                break;
        }

    }
}
