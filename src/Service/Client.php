<?php


namespace App\Service;


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

    public function getUsername()
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
    public function getId()
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
        if ($shipCount >= AbstractSocket::SHIP_COUNT) {
            return;
        }
        $this->ships[] = $ship;
        if ($shipCount >= AbstractSocket::SHIP_COUNT) {
            $this->isReady = true;
        }
    }

    /**
     * Check if any ship is hit
     *
     * @param int $x
     * @param int $y
     * @return bool
     */
    public function shoot(int $x, int $y): string
    {
        if (empty($this->ships)) {
            return Ship::STATUS_NOT_HIT;
        }
        foreach ($this->ships as $ship) {
            if ($ship->shoot($x, $y)) {
                if ($ship->isDown()) {
                    return Ship::STATUS_IS_DOWN;
                }
                return Ship::STATUS_IS_HIT;
            }
        }

        return Ship::STATUS_NOT_HIT;
    }

    public function isReady()
    {
        return $this->isReady;
    }
}
