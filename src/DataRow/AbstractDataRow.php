<?php

namespace App\DataRow;

use Zend\Hydrator\NamingStrategy\UnderscoreNamingStrategy;
use Zend\Hydrator\ObjectProperty as Hydrator;

/**
 * Class AbstractDataRow
 */
abstract class AbstractDataRow implements DataRowInterface
{
    /**
     * BaseEntity constructor.
     *
     * @todo rename datarow to entity
     * @param array $row .
     */
    public function __construct(array $row = null)
    {
        if ($row) {
            $this->getHydrator()->hydrate($row, $this);
        }
    }

    /**
     * Get Hydrator.
     *
     * @return Hydrator Hydrator
     */
    protected function getHydrator()
    {
        $hydrator = new Hydrator();
        $hydrator->setNamingStrategy(new UnderscoreNamingStrategy());

        return $hydrator;
    }

    /**
     * Extract objects to arrays.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->getHydrator()->extract($this);
    }

    /**
     * To json
     *
     * @return string Json
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }
}
