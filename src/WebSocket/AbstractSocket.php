<?php


namespace App\WebSocket;


use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use ReflectionClass;

/**
 * Class AbstractSocket
 */
abstract class AbstractSocket implements MessageComponentInterface
{

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
}
