<?php

namespace ParkStreet\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="unit")
 */
class Unit
{
    /**
     * This is the database ID, independent from the Unit ID.
     *
     * @var int
     *
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue()
     */
    private $id;

    /**
     * This is an assigned ID to the unit.
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="unit_id")
     */
    private $unitId;

    /**
     * @var Metric[]|Collection
     *
     * @ORM\OneToMany(targetEntity="\ParkStreet\Model\Metric", mappedBy="unit", cascade={"persist"})
     */
    private $metrics;

    /**
     * @param null|int $unitId
     */
    public function __construct($unitId = null)
    {
        $this->unitId = $unitId;
        $this->metrics = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getUnitId()
    {
        return $this->unitId;
    }

    /**
     * @return Metric[]|Collection
     */
    public function getMetrics()
    {
        return $this->metrics;
    }

    /**
     * @param Metric $metric
     */
    public function addMetric($metric)
    {
        $metric->setUnit($this);
        $this->metrics[] = $metric;
    }

    /**
     * @return Metric[]|Collection
     */
    public function getDownloadMetrics()
    {
        return $this->metrics->filter(function (Metric $metric) {
            return $metric->isDownload();
        });
    }

    /**
     * @return Metric[]|Collection
     */
    public function getUploadMetrics()
    {
        return $this->metrics->filter(function (Metric $metric) {
            return $metric->isUpload();
        });
    }

    /**
     * @return Metric[]|Collection
     */
    public function getLatencyMetrics()
    {
        return $this->metrics->filter(function (Metric $metric) {
            return $metric->isLatency();
        });
    }

    /**
     * @return Metric[]|Collection
     */
    public function getPacketLossMetrics()
    {
        return $this->metrics->filter(function (Metric $metric) {
            return $metric->isPacketLoss();
        });
    }
}
