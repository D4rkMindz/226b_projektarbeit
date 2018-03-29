<?php

namespace App\DataRow;

/**
 * Interface DataRowInterface.
 */
interface DataRowInterface
{
    /**
     * Extract objects to arrays.
     *
     * @return array
     */
    public function toArray();

    /**
     * Convert row to JSON.
     *
     * @return string JSON
     */
    public function toJson();
}
