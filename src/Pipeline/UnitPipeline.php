<?php


namespace ParkStreet\Pipeline;

use ParkStreet\Model\Metric;

class UnitPipeline
{
    /**
     * @var MetricPipeline
     */
    private $metricPipeline;

    /**
     * @param MetricPipeline $metricPipeline
     */
    public function __construct(MetricPipeline $metricPipeline)
    {
        $this->metricPipeline = $metricPipeline;
    }

    /**
     * @param array $metricData
     *
     * @return \Generator|Metric[]
     */
    public function generate(array $metricData)
    {
        $metricBatches = [];

        $metricBatches[] = $this->metricPipeline->generate($metricData['download'], Metric::DOWNLOAD);
        $metricBatches[] = $this->metricPipeline->generate($metricData['upload'], Metric::UPLOAD);
        $metricBatches[] = $this->metricPipeline->generate($metricData['latency'], Metric::LATENCY);
        $metricBatches[] = $this->metricPipeline->generate($metricData['packet_loss'], Metric::PACKET_LOSS);

        foreach ($metricBatches as $batch) {
            foreach ($batch as $metric) {
                yield $metric;
            }
        }
    }
}
