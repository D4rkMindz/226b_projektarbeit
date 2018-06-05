<?php


namespace App\WebSocket;

/**
 * Class Ship
 */
class Ship
{
    const STATUS_IS_DOWN = 'down';
    const STATUS_IS_HIT = 'hit';
    const STATUS_NOT_HIT = 'nothing';

    private $startX;
    private $startY;
    private $endX;
    private $endY;
    private $coordinatesHit;

    /**
     * Ship constructor.
     * @param int $startX
     * @param int $startY
     * @param int $endX
     * @param int $endY
     */
    public function __construct(int $startX, int $startY, int $endX, int $endY)
    {
        $this->startX = $startX;
        $this->startY = $startY;
        $this->endX = $endX;
        $this->endY = $endY;
        $this->coordinatesHit = ['x' => [], 'y' => []];
    }

    /**
     * Check if ship is hit
     *
     * @param int $x
     * @param int $y
     * @return bool true if hit
     */
    public function shoot(int $x, int $y)
    {
        if ($this->inRange($y, $this->startY, $this->endY) && $this->inRange($x, $this->startX, $this->endX)) {
            $this->coordinatesHit['x'][] = $x;
            $this->coordinatesHit['y'][] = $y;
            return true;
        }

        return false;
    }

    /**
     * Check if ship is completely hit
     *
     * @return bool
     */
    public function isDown()
    {
        $rangeX = $this->getRange($this->startX, $this->endX);
        $diffX = array_diff($rangeX, $this->coordinatesHit['x']);
        $rangeY = $this->getRange($this->startY, $this->endY);
        $diffY = array_diff($rangeY, $this->coordinatesHit['y']);
        return empty($diffX) && empty($diffY);
    }

    /**
     * Check if value in range.
     *
     * @param int $value
     * @param int $min
     * @param int $max
     * @return bool
     */
    private function inRange(int $value, int $min, int $max)
    {
        return $value >= $min && $value <= $max;
    }

    private function getRange(int $start, int $end)
    {
        return range($start, $end);
    }
}
