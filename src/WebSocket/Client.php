<?php


namespace App\WebSocket;


use Ratchet\ConnectionInterface;

class Client
{
    /**
     * @var ConnectionInterface
     */
    private $connection;

    /**
     * @var Ship[]
     */
    private $ships;

    /**
     * @var bool
     */
    private $isReady;

    /**
     * @var string
     */
    private $username;

    /**
     * Client constructor.
     * @param ConnectionInterface $connection
     * @param string $username
     */
    public function __construct(ConnectionInterface $connection, string $username)
    {
        $this->connection = $connection;
        $this->isReady = false;
        $this->username = $username;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Get Connection.
     *
     * @return ConnectionInterface
     */
    public function getConnection(): ConnectionInterface
    {
        return $this->connection;
    }

    /**
     * Get client connection id.
     *
     * @return mixed
     */
    public function getId(): string
    {
        return (string)$this->connection->resourceId;
    }

    /**
     * Emit message to client
     *
     * @param string $data
     */
    public function send(string $data)
    {
        $this->connection->send($data);
    }

    /**
     * Add ship to client.
     *
     * @param Ship $ship
     */
    public function addShip(Ship $ship)
    {
        $shipCount = count($this->ships);
        if ($shipCount >= ActionHandler::SHIP_COUNT) {
            return;
        }
        $this->ships[] = $ship;
        if ($shipCount >= ActionHandler::SHIP_COUNT) {
            $this->isReady = true;
        }
    }

    /**
     * Remove ship
     *
     * @param $id
     */
    public function removeShip($id)
    {
        if (empty($this->ships)) {
            return;
        }
        foreach ($this->ships as $key => $ship) {
            if ($ship->getId() === $id) {
                unset($this->ships[$key]);
            }
        }
    }

    /**
     * Check if any ship is hit
     *
     * @param int $x
     * @param int $y
     * @return bool
     */
    public function shoot(int $x, int $y): array
    {
        if (empty($this->ships)) {
            return Ship::STATUS_NOT_HIT;
        }
        foreach ($this->ships as $ship) {
            if ($ship->shoot($x, $y)) {
                if ($ship->isDown()) {
                    return ['status' => Ship::STATUS_IS_DOWN, 'ship' => $ship];
                }
                return ['status' => Ship::STATUS_IS_HIT];
            }
        }

        return ['status'=> Ship::STATUS_NOT_HIT];
    }

    public function isReady()
    {
        return $this->isReady;
    }

    public function setReady()
    {
        $this->isReady = true;
    }
}
