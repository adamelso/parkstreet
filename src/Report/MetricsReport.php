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
     * @var string
     */
    private $title;

    /**
     * @param string $title
     * @param array $metricDataByUnit
     * @param string $type
     * @internal param Aggregation[] $aggregations
     */
    private function __construct(string $title, array $metricDataByUnit, string $type)
    {
        $this->title = $title;
        $this->metricDataByUnit = $metricDataByUnit;
        $this->type = $type;
    }

    public static function forSingleUnit($unitId, $hour, $metrics, string $type)
    {
        $types = [
            Metric::DOWNLOAD    => 'Download',
            Metric::UPLOAD      => 'Upload',
            Metric::LATENCY     => 'Latency',
            Metric::PACKET_LOSS => 'Packet Loss',
        ];

        $title = sprintf('Aggregated %s Metrics for Unit %d at %02d:00', $types[$type], $unitId, $hour);

        return new self($title, [$unitId => $metrics], $type);
    }

    public static function forMultipleUnits($hour, array $metricDataByUnit, string $type)
    {
        $types = [
            Metric::DOWNLOAD    => 'Download',
            Metric::UPLOAD      => 'Upload',
            Metric::LATENCY     => 'Latency',
            Metric::PACKET_LOSS => 'Packet Loss',
        ];

        $title = sprintf('Aggregated %s Metrics at %02d:00', $types[$type], $hour);

        return new self($title, $metricDataByUnit, $type);
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return array[]
     */
    public function getTableRows(): array
    {
        $rows = [];

        foreach ($this->metricDataByUnit as $unitId => $metricData) {
            $aggregation = new MathPhpAggregation($metricData, $this->type);

            $rows[] = array_combine($this->getTableHeaders(), [
                $unitId,
                $aggregation->minimum(),
                $aggregation->maximum(),
                $aggregation->mean(),
                $aggregation->median()
            ]);
        }

        return $rows;
    }

    public function getTableHeaders(): array
    {
        return ['Unit Id', 'Minimum', 'Maximum', 'Mean', 'Median'];
    }
}
