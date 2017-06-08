<?php

namespace ParkStreet;

interface Aggregation
{
    /**
     * @return int
     */
    public function min(): int;

    /**
     * @return int
     */
    public function max(): int;

    /**
     * @return int
     */
    public function mean(): int;

    /**
     * @return int
     */
    public function median(): int;
}
