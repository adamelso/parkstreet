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
     * @var ObjectRepository
     */
    private $unitRepository;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(Client $client, Feed $feed, UnitPipeline $unitPipeline, ObjectRepository $unitRepository, EventDispatcherInterface $eventDispatcher)
    {
        $this->client = $client;
        $this->feed = $feed;
        $this->unitPipeline = $unitPipeline;
        $this->unitRepository = $unitRepository;
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

        foreach ($feedData as $unitData) {
            $unit = $this->findUnitOrCreateNew($unitData['unit_id']);

            $counter = 0;

            foreach ($this->unitPipeline->generate($unitData['metrics']) as $metric) {
                ++$counter;

                $unit->addMetric($metric);
                $this->eventDispatcher->dispatch('metric_import');

                if (0 === $counter % $batch) {
                    $this->eventDispatcher->dispatch(BatchImported::EVENT, new BatchImported($unit));
                }
            }

            $this->eventDispatcher->dispatch(UnitImported::EVENT, new UnitImported($unit));

            yield $unit;
        }

        $this->eventDispatcher->dispatch('import_complete');
    }

    /**
     * @param int $unitData
     *
     * @return null|Unit
     */
    private function findUnitOrCreateNew($unitId)
    {
        $unit = $this->unitRepository->findOneBy(['unitId' => $unitId]);

        if (! $unit) {
            $unit = new Unit($unitId);
        }

        return $unit;
    }
}
