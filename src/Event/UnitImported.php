<?php

namespace ParkStreet\Event;

use ParkStreet\Model\Unit;
use Symfony\Component\EventDispatcher\Event;

class UnitImported extends Event
{
    const EVENT = 'import_unit_complete';

    /**
     * @var Unit
     */
    private $unit;
    /**
     * @var int
     */
    private $metricImportCount;

    public function __construct(Unit $unit, int $metricImportCount)
    {
        $this->unit = $unit;
        $this->metricImportCount = $metricImportCount;
    }

    /**
     * @return Unit
     */
    public function getUnit(): Unit
    {
        return $this->unit;
    }

    /**
     * @return int
     */
    public function getMetricImportCount(): int
    {
        return $this->metricImportCount;
    }
}
