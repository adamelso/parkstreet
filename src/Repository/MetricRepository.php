<?php

namespace ParkStreet\Repository;

use Doctrine\ORM\EntityRepository;

class MetricRepository extends EntityRepository
{
    /**
     * @param int $unitId
     * @param string $type
     * @param int $hour
     *
     * @return int[]|float[]
     */
    public function selectUnitDataPointsByHour(int $unitId, string $type, int $hour)
    {
        $q = $this->_em->createQuery(<<<DQL
SELECT
  m.value
FROM
  ParkStreet\Model\Metric m
JOIN
  m.unit u
WHERE
  m.unit = :unit
    AND
  m.type = :type
    AND
  m.hour = :hour
DQL
        );

        $q->setParameter('unit', $unitId);
        $q->setParameter('type', $type);
        $q->setParameter('hour', $hour);

        return array_column($q->getArrayResult(), 'value');
    }

    /**
     * @param int $hour
     */
    public function selectAllUnitsDataPointsByHour(string $type, int $hour)
    {
        $q = $this->_em->createQuery(<<<DQL
SELECT
  u.id AS unit_id,
  m.value AS metric_value
FROM
  ParkStreet\Model\Metric m
JOIN
  m.unit u
WHERE
  m.type = :type
    AND
  m.hour = :hour
DQL
        );

        $q->setParameter('type', $type);
        $q->setParameter('hour', $hour);

        $result = [];

        foreach ($q->getArrayResult() as $row) {
            $result[$row['unit_id']][] = $row['metric_value'];
        }

        return $result;
    }
}
