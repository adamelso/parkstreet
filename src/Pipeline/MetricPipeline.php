<?php

namespace ParkStreet\Pipeline;

use ParkStreet\Model\Metric;

class MetricPipeline
{
    /**
     * @param array[] $dataPoints
     * @param string $type
     *
     * @return \Generator|Metric[]
     */
    public function generate(array $dataPoints, string $type)
    {
        foreach ($dataPoints as $dataPoint) {
            yield Metric::createFromDataPoint($dataPoint, $type);
        }
    }
}
