<?php


namespace App\WebSocket;


use Ratchet\ConnectionInterface;

class ActionHandler implements ObserverableInterface
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
     * @var Room[];
     */
    private $rooms = [];

    /**
     * Update event for observer
     *
     * @param ConnectionInterface $connection
     * @param string $type
     * @param array $data
     */
    public function update(ConnectionInterface $connection, string $type, array $data)
    {
        if ($type === self::ACTION_HOST) {
            $this->host($connection, $data['username']);
            return;
        }

        $room = $this->getRoomByClientId($connection->resourceId);
        if (empty($room)) {
            // TODO handle error on empty room;
            return;
        }
        $this->handle($connection, $type, $data, $room);
    }

    /**
     * Get Room by Client ID
     *
     * @param string $clientId
     * @return Room|null
     */
    private function getRoomByClientId(string $clientId)
    {
        foreach ($this->rooms as $key => $room) {
            if ($room->existsClient($clientId)) {
                return $room;
            }
        }
        return null;
    }

    /**
     * Handle Actions
     *
     * @param ConnectionInterface $connection
     * @param string $type
     * @param array $data
     * @param Room $room
     */
    private function handle(ConnectionInterface $connection, string $type, array $data, Room $room)
    {
        switch ($type) {
            case self::ACTION_JOIN:
                $client = new Client($connection, $data['username']);
                $room->attach($client);
                $room->emitJoin($client->getUsername());
                break;
            case self::ACTION_PLACE_SHIP:
                $room->addShip($connection->resourceId, $data['startX'],$data['startY'],$data['endX'],$data['endY']);
                break;
            case self::ACTION_READY:
                $room->emitReady($connection->resourceId);
                break;
            case self::ACTION_START:
                $room->emitStart();
                break;
            case self::ACTION_SHOT:
                $room->emitShot($connection->resourceId, $data['x'], $data['y']);
                break;
            default:
                break;
        }
    }

    /**
     * Host the game
     *
     * @param ConnectionInterface $connection
     * @param $username
     */
    private function host(ConnectionInterface $connection, $username)
    {
        $client = new Client($connection, $username);

        $roomId = uniqid();
        $this->rooms[$roomId] = new Room($roomId);
        $this->rooms[$roomId]->attach($client);
        $this->rooms[$roomId]->emitHost($client->getId(), $this->rooms[$roomId]->getId());
        return;
    }
}
