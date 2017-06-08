<?php
/**
 * @author    Adam Elsodaney <adam.elsodaney@reiss.com>
 * @date      2017-06-08
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace ParkStreet\Report;


use ParkStreet\Aggregation;
use ParkStreet\Aggregation\MathPhpAggregation;
use ParkStreet\Model\Unit;
use ParkStreet\Report;

class MetricsReport implements Report
{
    /**
     * @var Unit[]
     */
    private $units;

    /**
     * @var string
     */
    private $type;

    /**
     * @param array $units
     * @param string $type
     */
    public function __construct(array $units, string $type)
    {
        $this->units = $units;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        $type = ucwords(str_replace('_', ' ', $this->type));

        return count($this->units) === 1
            ? sprintf('Aggregated %s Metrics for Unit %d', $type, $this->units[0]->getUnitId())
            : sprintf('Aggregated %s Metrics', $type);
    }

    /**
     * @return array[]
     */
    public function getTableRows(): array
    {
        $rows = [];

        foreach ($this->units as $unit) {
            $aggregation = $this->getAggregation($unit, $this->type);

            $rows[] = [$unit->getUnitId(), $aggregation->min(), $aggregation->max(), $aggregation->mean(), $aggregation->median()];
        }

        return $rows;
    }

    private function getAggregation(Unit $unit, $type)
    {
        switch ($type) {
            case 'download':
                return new MathPhpAggregation($unit->getDownloadMetrics());
            case 'upload':
                return new MathPhpAggregation($unit->getUploadMetrics());
            case 'latency':
                return new MathPhpAggregation($unit->getLatencyMetrics());
            case 'packet_loss':
                return new MathPhpAggregation($unit->getPacketLossMetrics());
        }

        return null;
    }
}
