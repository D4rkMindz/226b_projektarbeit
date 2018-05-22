<?php


namespace App\Service;

/**
 * Class Room
 */
class Room
{
    /**
     * @var Client[]
     */
    private $clients;

    private $started = false;

    private $isFull = false;

    private $clientCount = 0;

    /**
     * Room constructor.
     */
    public function __construct()
    {
        $this->clients = [];
    }

    /**
     * Attach client to room
     *
     * @param Client $client
     * @return bool
     */
    public function attach(Client $client)
    {
        if ($this->clientCount >= 2 && $this->isFull) {
            return false;
        }
        $this->clients[$client->getId()] = $client;
        $this->clientCount++;
        if ($this->clientCount >= 2) {
            $this->isFull = true;
        }
        return true;
    }

    /**
     * Check if client is in current room
     *
     * @todo find more efficient solution
     *
     * @param string $clientId
     * @return bool
     */
    public function existsClient(string $clientId)
    {
        foreach ($this->clients as $client) {
            if ($client->getId() === $clientId) {
                return true;
            }
        }
        return false;
    }

    /**
     * Emit message to users
     *
     * @param string $from
     * @param int $x
     * @param int $y
     */
    public function emitShot(string $from, int $x, int $y)
    {
        $shipStatus = Ship::STATUS_NOT_HIT;
        foreach ($this->clients as $client) {
            if ($client->getId() == $from) {
                continue;
            }
            $shipStatus = $client->shoot($x, $y);
        }

        $package = [
            'type' => AbstractSocket::ACTION_SHOT,
            'x' => $x,
            'y' => $y,
            'ship_status' => $shipStatus,
            'status' => 'success',
        ];

        $this->sendDataToClients($package);
    }

    /**
     * Emit join message.
     *
     * @param string $username
     */
    public function emitJoin(string $username)
    {
        $package = [
            'type' => AbstractSocket::ACTION_JOIN,
            'username' => $username,
        ];
        $this->sendDataToClients($package);
    }

    /**
     * Emit ready.
     *
     * @param string $username
     */
    public function emitReady(string $username)
    {
        $package = [
            'type' => AbstractSocket::ACTION_READY,
            'username' => $username,
        ];
        $this->sendDataToClients($package);
    }

    /**
     * Emit start.
     */
    public function emitStart()
    {
        $this->started = true;
        $package = [
            'type' => AbstractSocket::ACTION_START,
        ];
        $this->sendDataToClients($package);
    }

    /**
     * Add ship to client
     *
     * @param string $clientId
     * @param int $startX
     * @param int $startY
     * @param int $endX
     * @param int $endY
     */
    public function addShip(string $clientId, int $startX, int $startY, int $endX, int $endY)
    {
        $ship = new Ship($startX, $startY, $endX, $endY);
        $client = $this->clients[$clientId];
        $client->addShip($ship);
        if ($client->isReady()) {
            $this->emitReady($client->getUsername());
        }

        $canStart = true;
        foreach ($this->clients as $client) {
            if (!$client->isReady()) {
                $canStart = false;
            }
        }

        if ($canStart) {
            $this->emitStart();
        }
    }

    /**
     * Send data to clients
     *
     * @param array $package
     */
    private function sendDataToClients(array $package)
    {
        foreach ($this->clients as $client) {
            $client->send(json_encode($package));
        }
    }
}
