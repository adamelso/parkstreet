<?php

namespace ParkStreet\Aggregation;

use MathPHP\Statistics\Average;
use ParkStreet\Aggregation;

class MathPhpAggregation implements Aggregation
{
    /**
     * @var float[]
     */
    private $data;

    /**
     * @param int[]|float[] $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return int
     */
    public function max(): int
    {
        return (int) max($this->data);
    }

    /**
     * @return int
     */
    public function min(): int
    {
        return (int) min($this->data);
    }

    /**
     * @return int
     */
    public function mean(): int
    {
        return (int) round(Average::mean($this->data), 0);
    }

    /**
     * @return int
     */
    public function median(): int
    {
        return (int) Average::median($this->data);
    }
}
