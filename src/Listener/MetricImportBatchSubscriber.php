<?php

namespace ParkStreet\Listener;

use Doctrine\Common\Persistence\ObjectManager;
use ParkStreet\Event\BatchImported;
use ParkStreet\Event\UnitImported;
use ParkStreet\Model\Metric;
use ParkStreet\Model\Unit;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class MetricImportBatchSubscriber implements EventSubscriberInterface
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function onImportBatchComplete(BatchImported $event)
    {
        $this->persistUnit($event->getUnit());
    }

    public function onImportUnitComplete(UnitImported $event)
    {
        $this->persistUnit($event->getUnit());
        $this->objectManager->clear(Metric::class);
        $this->objectManager->clear(Unit::class);
    }

    private function persistUnit(Unit $unit)
    {
        $this->objectManager->persist($unit);
        $this->objectManager->flush();
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     */
    public static function getSubscribedEvents()
    {
        return [
            BatchImported::EVENT => 'onImportBatchComplete',
            UnitImported::EVENT  => 'onImportUnitComplete',
        ];
    }
}
