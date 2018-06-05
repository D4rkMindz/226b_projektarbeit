<?php
/**
 * Created by PhpStorm.
 * User: Björn
 * Date: 05.06.2018
 * Time: 10:26
 */

namespace App\WebSocket;


use Ratchet\ConnectionInterface;

interface ObserverableInterface
{
    public function update(ConnectionInterface $connection, string $type, array $data);
}
