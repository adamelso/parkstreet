<?php
/**
 * @author    Adam Elsodaney <adam.elsodaney@reiss.com>
 * @date      2017-06-01
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace ParkStreet;

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
