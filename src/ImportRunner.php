<?php

namespace ParkStreet;

use Doctrine\Common\Persistence\ObjectRepository;
use ParkStreet\Event\BatchImported;
use ParkStreet\Event\UnitImported;
use ParkStreet\Model\Unit;
use ParkStreet\Pipeline\MetricPipeline;
use ParkStreet\Pipeline\UnitPipeline;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ImportRunner
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
    private $unitPipeline;

    /**
     * @var UnitProvider
     */
    private $unitProvider;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(Client $client, Feed $feed, UnitPipeline $unitPipeline, UnitProvider $unitProvider, EventDispatcherInterface $eventDispatcher)
    {
        $this->client = $client;
        $this->feed = $feed;
        $this->unitPipeline = $unitPipeline;
        $this->unitProvider = $unitProvider;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param int $batch
     *
     * @return \Generator|Unit[]
     */
    public function run(int $batch = 400)
    {
        $stream   = $this->client->connect();
        $feedData = $this->feed->process($stream);

        $this->eventDispatcher->dispatch('import_start');

        $counter = 0;

        foreach ($feedData as $unitData) {
            $unit = $this->unitProvider->createIfNotExist($unitData['unit_id']);

            // @todo To ensure duplicate data isn't imported, we may need to set a unique constraint for the metric table.
            // Until then, ignore.
            if (! $unit) {
                continue;
            }

            $metricCountForUnit = 0;

            foreach ($this->unitPipeline->generate($unitData['metrics']) as $metric) {
                ++$counter;
                ++$metricCountForUnit;

                $unit->addMetric($metric);
                $this->eventDispatcher->dispatch('metric_import');

                if (0 === $counter % $batch) {
                    $this->eventDispatcher->dispatch(BatchImported::EVENT, new BatchImported($unit));
                }
            }

            $this->eventDispatcher->dispatch(UnitImported::EVENT, new UnitImported($unit, $metricCountForUnit));

            yield $unit;
        }

        $this->eventDispatcher->dispatch('import_complete');
    }

    /**
     * @param Client $client
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
    }

}
