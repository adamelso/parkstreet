<?php

namespace ParkStreet;

use Doctrine\Common\Persistence\ObjectManager;
use ParkStreet\Model\Metric;
use ParkStreet\Model\Unit;

class Import
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var Feed
     */
    private $feed;
    /**
     * @var MetricPipeline
     */
    private $metricPipeline;
    /**
     * @var ObjectManager
     */
    private $objectManager;

    public function __construct(Client $client, Feed $feed, MetricPipeline $metricPipeline, ObjectManager $objectManager)
    {
        $this->client = $client;
        $this->feed = $feed;
        $this->metricPipeline = $metricPipeline;
        $this->objectManager = $objectManager;
    }

    /**
     * @param int $batch
     *
     * @return \Generator|Unit[]
     */
    public function run(int $batch = 100)
    {
        $stream   = $this->client->connect();
        $feedData = $this->feed->process($stream);

        foreach ($feedData as $unitData) {
            $unit = new Unit($unitData['unit_id']);

            $downloadMetrics   = $this->metricPipeline->generate($unitData['metrics']['download'], Metric::DOWNLOAD);
            $uploadMetrics     = $this->metricPipeline->generate($unitData['metrics']['upload'], Metric::UPLOAD);
            $latencyMetrics    = $this->metricPipeline->generate($unitData['metrics']['latency'], Metric::LATENCY);
            $packetLossMetrics = $this->metricPipeline->generate($unitData['metrics']['packet_loss'], Metric::PACKET_LOSS);

            $counter = 0;

            foreach ($downloadMetrics as $metric) {
                ++$counter;

                $unit->addMetric($metric);

                if (0 === $counter % $batch) {
                    $this->objectManager->persist($unit);
                    $this->objectManager->flush();
                }
            }

            foreach ($uploadMetrics as $metric) {
                $unit->addMetric($metric);

                if (0 === $counter % $batch) {
                    $this->objectManager->persist($unit);
                    $this->objectManager->flush();
                }
            }

            foreach ($latencyMetrics as $metric) {
                $unit->addMetric($metric);

                if (0 === $counter % $batch) {
                    $this->objectManager->persist($unit);
                    $this->objectManager->flush();
                }
            }

            foreach ($packetLossMetrics as $metric) {
                $unit->addMetric($metric);

                if (0 === $counter % $batch) {
                    $this->objectManager->persist($unit);
                    $this->objectManager->flush();
                }
            }

            yield $unit;
        }
    }
}
