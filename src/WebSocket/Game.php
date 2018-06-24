<?php


namespace App\WebSocket;


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
     * @var Observer
     */
    private $observer;

    /**
     * Game constructor.
     * @param Observer $observer
     * @throws ReflectionException
     */
    public function __construct(Observer $observer)
    {
        parent::__construct();
        $this->clients = new SplObjectStorage();
        $this->observer = $observer;
    }

    /**
     * When a new connection is opened it will be passed to this method
     * @param  ConnectionInterface $conn The socket/connection that just connected to your application
     * @throws \Exception
     */
    function onOpen(ConnectionInterface $conn)
    {
        echo $conn->resourceId . " joined\n";
    }

    /**
     * This is called before or after a socket is closed (depends on how it's closed).  SendMessage to $conn will not result in an error if it has already been closed.
     * @param  ConnectionInterface $conn The socket/connection that is closing/closed
     * @throws \Exception
     */
    function onClose(ConnectionInterface $conn)
    {
        echo "{$conn->resourceId} left\n";
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
        echo "ERROR ON {$conn->resourceId}. Exception: {$e->getMessage()}";
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
            return;
        }

        $this->observer->notify($from, $data['type'], $data);
    }
}
