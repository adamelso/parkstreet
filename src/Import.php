<?php

namespace ParkStreet;

use ParkStreet\Client\OfflineClient;
use ParkStreet\Factory\UnitFactory;
use ParkStreet\Model\Unit;
use Zend\Hydrator\HydrationInterface;

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
     * @var UnitFactory
     */
    private $unitFactory;

    /**
     * @var HydrationInterface
     */
    private $hydrator;

    public function __construct(Client $client, Feed $feed, UnitFactory $unitFactory, HydrationInterface $hydrator)
    {
        $this->client = $client;
        $this->feed = $feed;
        $this->unitFactory = $unitFactory;
        $this->hydrator = $hydrator;
    }

    /**
     * @return \Generator|Unit[]
     */
    public function run()
    {
        $stream   = $this->client->connect();
        $feedData = $this->feed->process($stream);

        foreach ($feedData as $unitData) {
            $unit = $this->unitFactory->createNew();

            $unitData['number'] = $unitData['unit_id'];
            $unitData['id']     = null;

            $this->hydrator->hydrate($unitData, $unit);

            yield $unit;
        }
    }
}
