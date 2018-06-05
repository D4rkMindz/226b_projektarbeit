<?php
/**
 * Created by PhpStorm.
 * User: Björn
 * Date: 29.05.2018
 * Time: 10:14
 */

namespace App\Test\Service;

use App\WebSocket\Client;
use App\WebSocket\Room;
use App\Test\MockConnection;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class RoomTest extends TestCase
{
    /**
     * @var Room
     */
    private $room;

    /**
     * @var Client
     */
    private $client;

    public function setUp()
    {
        $this->room = new Room();
        $connection = new MockConnection();
        $this->client = new Client($connection, 'username');
    }

    public function test()
    {
        $this->assertTrue($this->room->attach($this->client));
        $reflection = new ReflectionClass($this->client);
        $clients = $reflection->getProperty('clients')->getValue();
        $a = 1;
    }
}
