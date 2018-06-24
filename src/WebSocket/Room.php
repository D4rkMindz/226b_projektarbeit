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
     * Check if room is full (maximum of players reached)
     *
     * @return bool
     */
    public function isFull(): bool
    {
        return $this->isFull;
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
     * Get clients.
     *
     * @param string $clientId
     * @return Client|bool|mixed
     */
    public function getClient(string $clientId)
    {
        foreach ($this->clients as $client) {
            if ($client->getId() === $clientId) {
                return $client;
            }
        }
        return false;
    }

    /**
     * Check if all clients ready.
     *
     * @return bool
     */
    public function allClientsReady()
    {
        $allReady = true;
        foreach ($this->clients as $client) {
            if (!$client->isReady()) {
                $allReady = false;
            }
        }
        return $allReady;
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
        $username = '';
        $winner = null;
        $looser = null;
        $ship = ['status' => Ship::STATUS_NOT_HIT];
        $allShipsDown = false;
        foreach ($this->clients as $client) {
            if ($client->getId() === $from) {
                $username = $client->getUsername();
                continue;
            }
            $ship = $client->shoot($x, $y);
            $allShipsDown = $client->allShipsDown();
            if ($allShipsDown) {
                $looser = $client->getUsername();
            }
        }

        if ($allShipsDown) {
            $winner = $username;
        }
        $shipStatus = $ship['status'];

        $package = [
            'type' => ActionHandler::ACTION_SHOT,
            'x' => $x,
            'y' => $y,
            'ship_status' => $shipStatus,
            'status' => 'success',
            'source' => $username,
            'ship_length' => null,
            'ship_id' => null,
            'victory' => $allShipsDown,
            'winner' => $winner,
            'looser' => $looser,
        ];

        if ($ship['status'] === Ship::STATUS_IS_DOWN) {
            $package['ship_length'] = $ship['ship']->getLength();
            $package['ship_id'] = $ship['ship']->getId();
        }
        $this->sendDataToClients($package);
    }

    /**
     * Emit join message.
     *
     * @param string $userId
     * @param string $username
     */
    public function emitJoin(string $userId, string $username)
    {
        $package = [
            'type' => ActionHandler::ACTION_JOIN,
            'username' => $username,
        ];
        $this->broadcast($userId, $package);

        $enemyClient = null;
        foreach ($this->clients as $client) {
            if ($client->getId() !== $userId) {
                $enemyClient = $client;
            }
        }

        $isReady = false;
        if (!empty($enemyClient)) {
            $isReady = $enemyClient->isReady();
        }

        $this->sendToEmitter($userId, ['enemy_ready' => $isReady, 'type' => ActionHandler::ACTION_JOIN_INFO]);
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
        $this->broadcast($userId, $package);
        $this->emitStart($userId);
    }

    /**
     * Emit start.
     */
    public function emitStart($userId)
    {
        if (!($this->allClientsReady() && count($this->clients) > 1)) {
            $this->sendToEmitter($userId, ['error' => 'Someone is not ready']);
            return;
        }
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

    public function emitLeave(string $userId)
    {
        $username = $this->getClient($userId)->getUsername();
        unset($this->clients[$userId]);
        $this->sendDataToClients(['username' => $username, 'type' => ActionHandler::ACTION_LEAVE]);
    }

    /**
     * Add ship to client
     *
     * @param string $clientId
     * @param int $startX
     * @param int $startY
     * @param int $endX
     * @param int $endY
     * @param $id
     */
    public function addShip(string $clientId, int $startX, int $startY, int $endX, int $endY, $id)
    {
        $ship = new Ship($id, $startX, $startY, $endX, $endY);
        $client = $this->clients[$clientId];
        $client->addShip($ship);
    }

    /**
     * Remove ship.
     *
     * @param string $clientId
     * @param $id
     */
    public function removeShip(string $clientId, $id)
    {
        $client = $this->clients[$clientId];
        $client->removeShip($id);
    }

    /**
     * Send data to clients
     *
     * @param array $package
     */
    private function sendDataToClients(array $package)
    {
        // TODO maybe move type to parameters
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
