<?php

namespace ParkStreet\Aggregation;

use Doctrine\Common\Collections\Collection;
use MathPHP\Statistics\Average;
use ParkStreet\Aggregation;
use ParkStreet\Model\Metric;

class MathPhpAggregation implements Aggregation
{
    /**
     * @var float[]
     */
    private $data;

    /**
     * @param Metric[] $metrics
     */
    public function __construct($metrics)
    {
        $metrics = $metrics instanceof Collection ? $metrics->toArray() : $metrics;

        $data = array_map(function (Metric $metric) {
            return $metric->getValue();
        }, $metrics);

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
