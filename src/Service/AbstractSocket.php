<?php


namespace App\Service;


use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use ReflectionClass;

/**
 * Class AbstractSocket
 */
abstract class AbstractSocket implements MessageComponentInterface
{
    /**
     * All valid actions as constant with ACTION prefix
     */
    const ACTION_JOIN = 'join'; // join room
    const ACTION_HOST = 'host'; // create new room as host
    const ACTION_PLACE_SHIP = 'place-ship'; // place a ship on the board
    const ACTION_READY = 'ready'; // ready to play
    const ACTION_START = 'start'; // start the game
    const ACTION_SHOT = 'shot'; // fire a shot

    /**
     * All
     */
    const SHIP_COUNT = 7; // count of the ships
    const SHIP_ONE = 1; // ship with a length of one blocks
    const SHIP_TWO = 2; // ship with a length of two blocks
    const SHIP_THREE = 2; // ship with a length of three blocks
    const SHIP_FOUR = 1; // ship with a length of four blocks
    const SHIP_FIVE = 1; // ship with a length of five blocks

    /**
     * @var Room[]
     */
    private $rooms;

    /**
     * @var array
     */
    private $validActions;

    /**
     * AbstractSocket constructor.
     * @throws \ReflectionException
     */
    public function __construct()
    {
        $this->rooms = [];

        $refl = new ReflectionClass(get_class());
        $this->validActions = [];
        foreach ($refl->getConstants() AS $key => $value) {
            if (substr($key, 0, 6) === 'ACTION') {
                $action = strtolower($value);
                $this->validActions[$action] = 1;
            }
        }
    }

    /**
     * @param string $action
     * @return bool
     */
    protected function isValidAction(string $action): bool
    {
        return array_key_exists(strtolower($action), $this->validActions);
    }

    /**
     * Add client
     *
     * @param ConnectionInterface $connection
     * @param string $roomId
     * @param string $username
     * @return bool true if the use joined the room
     */
    protected function addClientToRoom(ConnectionInterface $connection, string $roomId, string $username): bool
    {
        if ($this->existsRoom($roomId)) {
            $hasJoined = $this->rooms[$roomId]->attach(new Client($connection, $username));
            if ($hasJoined) {
                $this->emitJoin($roomId, $username);
                return true;
            } else {
                $this->emitError($connection, 'Session is full');
                return false;
            }
        }
        return false;
    }

    /**
     * @param ConnectionInterface $connection
     * @param string $username
     * @return string Room ID
     */
    protected function addClientAsHost(ConnectionInterface $connection, string $username): string
    {
        $roomId = uniqid();
        $this->rooms[$roomId] = new Room();
        $this->rooms[$roomId]->attach(new Client($connection, $username));
        echo "HOSTING {$roomId}";
        return $roomId;
    }

    /**
     * Get connections in room
     *
     * @param string $roomId
     * @return Room
     */
    protected function getRoom(string $roomId): Room
    {
        return $this->rooms[$roomId];
    }

    /**
     * Get room id by client id.
     *
     * @param string $clientId
     * @return bool|int|string
     */
    protected function getRoomByClientId(string $clientId)
    {
        foreach ($this->rooms as $roomId => $room) {
            if ($room->existsClient($clientId)) {
                return $roomId;
            }
        }
        return false;
    }

    /**
     * Check if room exists
     *
     * @param string $roomId
     * @return bool
     */
    protected function existsRoom(string $roomId): bool
    {
        return array_key_exists($roomId, $this->rooms);
    }

    /**
     * Emit shot to room
     *
     * @param string $roomId
     * @param string $from
     * @param int $x
     * @param int $y
     */
    protected function emitShot(string $roomId, string $from, int $x, int $y)
    {
        $room = $this->getRoom($roomId);
        $room->emitShot($from, $x, $y);
    }

    /**
     * Emit join.
     *
     * @param string $roomId
     * @param string $username
     */
    protected function emitJoin(string $roomId, string $username)
    {
        $room = $this->getRoom($roomId);
        $room->emitJoin($username);
    }

    /**
     * Emit host.
     *
     * @param string $roomId
     * @param string $userId
     * @param string $hostId
     */
    protected function emitHost(string $roomId, string $userId, string $hostId)
    {
        $room = $this->getRoom($roomId);
        $room->emitHost($userId, $hostId);
    }

    /**
     * Emit start.
     *
     * @param string $roomId
     */
    protected function emitStart(string $roomId)
    {
        $room = $this->getRoom($roomId);
        $room->emitStart();
    }

    /**
     * Send information for invalid socket event
     *
     * @param ConnectionInterface $connection
     * @param null|string $message
     */
    protected function emitError(ConnectionInterface $connection, ?string $message = 'not allowed')
    {
        $connection->send(json_encode(['status' => 'error', 'message' => $message, 'type' => 'error']));
    }

    /**
     * Place ship.
     *
     * @param string $roomId
     * @param string $playerId
     * @param int $startX
     * @param int $startY
     * @param int $endX
     * @param int $endY
     */
    protected function addShip(string $roomId, string $playerId, int $startX, int $startY, int $endX, int $endY)
    {
        $room = $this->getRoom($roomId);
        $room->addShip($playerId, $startX, $startY, $endX, $endY);
    }
}
