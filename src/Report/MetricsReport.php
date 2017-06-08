<?php

namespace ParkStreet\Report;

use ParkStreet\Aggregation;
use ParkStreet\Aggregation\MathPhpAggregation;
use ParkStreet\Model\Metric;
use ParkStreet\Report;

class MetricsReport implements Report
{
    /**
     * Unit ID to metrics map.
     *
     * [
     *     7 => [1702130, 1685680, .. ],
     *     3 => [ ... ],
     *     ...
     * ]
     *
     * @var array
     */
    private $metricDataByUnit;

    /**
     * @var string
     */
    private $type;

    /**
     * @param Aggregation[] $aggregations
     * @param string $type
     */
    public function __construct(array $metricDataByUnit, string $type)
    {
        $this->metricDataByUnit = $metricDataByUnit;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        $types = [
            Metric::DOWNLOAD    => 'Download',
            Metric::UPLOAD      => 'Upload',
            Metric::LATENCY     => 'Latency',
            Metric::PACKET_LOSS => 'Packet Loss',
        ];

        return count($this->metricDataByUnit) === 1
            ? sprintf('Aggregated %s Metrics for Unit %d', $types[$this->type], key($this->metricDataByUnit))
            : sprintf('Aggregated %s Metrics', $types[$this->type]);
    }

    /**
     * @return array[]
     */
    public function getTableRows(): array
    {
        $rows = [];

        foreach ($this->metricDataByUnit as $unitId => $metricData) {
            $aggregation = new MathPhpAggregation($metricData, $this->type);

            $rows[] = [$unitId, $aggregation->min(), $aggregation->max(), $aggregation->mean(), $aggregation->median()];
        }

        return $rows;
    }
}
