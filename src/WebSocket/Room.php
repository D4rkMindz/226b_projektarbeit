<?php


namespace App\WebSocket;

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

    private $id;

    /**
     * Room constructor.
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->clients = [];
        $this->id = $id;
    }

    /**
     * Get room id.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
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
            if ($client->getId() === $from) {
                continue;
            }
            $shipStatus = $client->shoot($x, $y);
        }

        $package = [
            'type' => ActionHandler::ACTION_SHOT,
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
            'type' => ActionHandler::ACTION_JOIN,
            'username' => $username,
        ];
        $this->sendDataToClients($package);
    }

    /**
     * Emit ready.
     *
     * @param string $userId
     */
    public function emitReady(string $userId)
    {
        $username = '';
        foreach ($this->clients as $client) {
            if ($client->getId() === $userId) {
                $username = $client->getUsername();
                break;
            }
        }
        if (empty($username)) {
            // TODO handle empty username on emit ready
            return;
        }

        $package = [
            'type' => ActionHandler::ACTION_READY,
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
        reset($this->clients);
        $key = key($this->clients);
        $package = [
            'type' => ActionHandler::ACTION_START,
            'beginner' => $this->clients[$key]->getUsername(),
        ];
        $this->sendDataToClients($package);
    }

    /**
     * Emit host.
     *
     * @param string $userId
     * @param string $hostId
     */
    public function emitHost(string $userId, string $hostId)
    {
        $package = [
            'type' => ActionHandler::ACTION_HOST,
            'session_key' => $hostId,
        ];
        $this->sendToEmitter($userId, $package);
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
            $package['clientId'] = $client->getId();
            $client->send(json_encode($package));
        }
    }

    private function sendToEmitter(string $userId, array $package)
    {
        foreach ($this->clients as $client) {
            if ($client->getId() == $userId) {
                $client->send(json_encode($package));
            }
        }
    }

    private function broadcast(string $userId, array $package)
    {
        foreach ($this->clients as $client) {
            if ($client->getId() == $userId) {
                continue;
            }
            $client->send(json_encode($package));
        }
    }
}
