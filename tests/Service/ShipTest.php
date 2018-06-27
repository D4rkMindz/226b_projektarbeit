<?php
/**
 * Created by PhpStorm.
 * User: Björn
 * Date: 29.05.2018
 * Time: 09:22
 */

namespace App\Test\Service;


use App\WebSocket\Ship;
use PHPUnit\Framework\TestCase;

/**
 * Class ShipTest
 *
 * @coversDefaultClass \App\WebSocket\Ship
 */
class ShipTest extends TestCase
{
    /**
     * @var Ship
     */
    private $ship;

    // dont change this values. The test itself will fail.
    private $startX = 1;
    private $startY = 1;
    private $endX = 1;
    private $endY = 5;

    /**
     * Setup before test
     */
    public function setUp()
    {
        $this->ship = new Ship(1, $this->startX, $this->startY, $this->endX, $this->endY);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf(Ship::class, $this->ship);
        $this->assertSame(Ship::STATUS_NOT_HIT, 'nothing');
        $this->assertSame(Ship::STATUS_IS_HIT, 'hit');
        $this->assertSame(Ship::STATUS_IS_DOWN, 'down');
    }

    /**
     * Test shoot and isDown
     *
     * @covers ::isDown
     * @covers ::shoot
     */
    public function testShoot()
    {
        $this->assertFalse($this->ship->isDown());
        $this->assertTrue($this->ship->shoot($this->startX, $this->startY));
        $this->assertFalse($this->ship->shoot($this->startX + 1, $this->startY));
        $this->assertFalse($this->ship->isDown());
        $this->ship->shoot(1, 2);
        $this->ship->shoot(1, 3);
        $this->ship->shoot(1, 4);
        $this->ship->shoot(1, 5);
        $this->assertTrue($this->ship->isDown());
    }
}
