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

    public function __construct(Unit $unit)
    {
        $this->unit = $unit;
    }

    /**
     * @return Unit
     */
    public function getUnit(): Unit
    {
        return $this->unit;
    }
}
