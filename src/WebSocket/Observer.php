<?php


namespace App\WebSocket;

use Ratchet\ConnectionInterface;

class Observer
{
    /**
     * @var ObserverableInterface[]
     */
    private $observables = [];

    public function notify(ConnectionInterface $connection, string $type, array $data)
    {
        foreach ($this->observables as $observerable) {
            $observerable->update($connection, $type, $data);
        }
    }

    public function attach(ObserverableInterface $observerable)
    {
        $this->observables[] = $observerable;
    }

    public function detach(ObserverableInterface $observable)
    {
        foreach ($this->observables as $key => $obs) {
            if ($obs === $observable) {
                unset($this->observables[$key]);
            }
        }
    }
}
