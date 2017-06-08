<?php

namespace ParkStreet\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="ParkStreet\Repository\MetricRepository"))
 *
 * @ORM\Table(
 *     name="metric",
 *     indexes={
 *         @ORM\Index(name="metrics_by_hour_index", columns={"type", "hour"})
 *     }
 * )
 *
 */
class Metric
{
    const UPLOAD = 'U';
    const DOWNLOAD = 'D';
    const LATENCY = 'L';
    const PACKET_LOSS = 'P';

    const VALID_TYPES = [
        self::UPLOAD,
        self::DOWNLOAD,
        self::LATENCY,
        self::PACKET_LOSS,
    ];

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue()
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * For faster searching, we can index the database by the hour of the timestamp.
     *
     * @var int
     *
     * @ORM\Column(type="integer", length=2)
     */
    private $hour;

    /**
     * @var int
     *
     * @ORM\Column(type="float")
     */
    private $value;

    /**
     * Can only be one of the 4 constant values above.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=1)
     */
    private $type;

    /**
     * @var Unit
     *
     * @ORM\ManyToOne(targetEntity="\ParkStreet\Model\Unit", inversedBy="metrics")
     */
    private $unit;

    /**
     * @param string $type
     */
    public function __construct(string $type)
    {
        if (! in_array($type, self::VALID_TYPES)) {
            throw new \LogicException('Metric type is not valid');
        }

        $this->type = $type;
    }

    /**
     * @param array $dataPoint
     * @param string $type
     *
     * @return Metric
     */
    public static function createFromDataPoint(array $dataPoint, string $type)
    {
        $metric = new self($type);

        // We will assume the timestamps are UTC - but if they were not, we could pass in the timezone here.
        $metric->date  = \DateTime::createFromFormat('Y-m-d H:i:s', $dataPoint['timestamp']);
        $metric->hour  = (int) $metric->date->format('H');
        $metric->value = $dataPoint['value'];

        return $metric;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return int
     */
    public function getHour()
    {
        return $this->hour;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return Unit
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @param Unit $unit
     */
    public function setUnit(Unit $unit)
    {
        $this->unit = $unit;
    }

    /**
     * @return bool
     */
    public function isDownload(): bool
    {
        return self::DOWNLOAD === $this->type;
    }

    /**
     * @return bool
     */
    public function isUpload(): bool
    {
        return self::UPLOAD === $this->type;
    }

    /**
     * @return bool
     */
    public function isLatency(): bool
    {
        return self::LATENCY === $this->type;
    }

    /**
     * @return bool
     */
    public function isPacketLoss(): bool
    {
        return self::PACKET_LOSS === $this->type;
    }
}
