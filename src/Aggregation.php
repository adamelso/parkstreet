<?php

namespace ParkStreet;

interface Aggregation
{
    /**
     * @return int
     */
    public function minimum(): int;

    /**
     * @return int
     */
    public function maximum(): int;

    /**
     * @return int
     */
    public function mean(): int;

    /**
     * @return int
     */
    public function median(): int;
}
