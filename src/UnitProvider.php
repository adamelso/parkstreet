<?php

namespace ParkStreet;

use Doctrine\Common\Persistence\ObjectRepository;
use ParkStreet\Model\Unit;

class UnitProvider
{
    /**
     * @var ObjectRepository
     */
    private $repository;

    public function __construct(ObjectRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param int $unitData
     *
     * @return null|Unit
     */
    public function createIfNotExist($unitId)
    {
        $unit = $this->repository->findOneBy(['unitId' => $unitId]);

        if (! $unit) {
            return new Unit($unitId);
        }

        return null;
    }

    /**
     * @param int $unitData
     *
     * @return Unit
     */
    public function findOrCreateNew($unitId)
    {
        /** @var null|Unit $unit */
        $unit = $this->repository->findOneBy(['unitId' => $unitId]);

        return $unit ?: new Unit($unitId);
    }
}
