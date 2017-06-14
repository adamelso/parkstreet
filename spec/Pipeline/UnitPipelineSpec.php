<?php

namespace spec\ParkStreet\Pipeline;

use ParkStreet\Model\Metric;
use ParkStreet\Pipeline\MetricPipeline;
use ParkStreet\Pipeline\UnitPipeline;
use PhpSpec\Exception\Example\FailureException;
use PhpSpec\ObjectBehavior;

class UnitPipelineSpec extends ObjectBehavior
{
    function let(MetricPipeline $metricPipeline)
    {
        $this->beConstructedWith($metricPipeline);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(UnitPipeline::class);
    }

    function it_generates_metrics_from_a_pipeline_for_each_metric_type(MetricPipeline $metricPipeline)
    {
        $metricPipeline->generate([
            ['value' => 4589350, 'timestamp' => '2017-01-01 01:00:00'],
            ['value' => 5256140, 'timestamp' => '2017-01-01 02:00:00'],
            ['value' => 3976530, 'timestamp' => '2017-01-01 03:00:00'],
        ], 'D')->willReturn([
            Metric::createFromDataPoint(['value' => 4589350, 'timestamp' => '2017-01-01 01:00:00'], 'D'),
            Metric::createFromDataPoint(['value' => 5256140, 'timestamp' => '2017-01-01 02:00:00'], 'D'),
            Metric::createFromDataPoint(['value' => 3976530, 'timestamp' => '2017-01-01 03:00:00'], 'D'),
        ]);

        $metricPipeline->generate([
            ['value' => 1158380, 'timestamp' => '2017-01-01 01:00:00'],
            ['value' => 1663000, 'timestamp' => '2017-01-01 02:00:00'],
            ['value' => 2301060, 'timestamp' => '2017-01-01 03:00:00'],
        ], 'U')->willReturn([
            Metric::createFromDataPoint(['value' => 1158380, 'timestamp' => '2017-01-01 01:00:00'], 'U'),
            Metric::createFromDataPoint(['value' => 1663000, 'timestamp' => '2017-01-01 02:00:00'], 'U'),
            Metric::createFromDataPoint(['value' => 2301060, 'timestamp' => '2017-01-01 03:00:00'], 'U'),
        ]);

        $metricPipeline->generate([
            ['value' => 44856, 'timestamp' => '2017-01-01 01:00:00'],
            ['value' => 53795, 'timestamp' => '2017-01-01 02:00:00'],
            ['value' => 55940, 'timestamp' => '2017-01-01 03:00:00'],
        ], 'L')->willReturn([
            Metric::createFromDataPoint(['value' => 44856, 'timestamp' => '2017-01-01 01:00:00'], 'L'),
            Metric::createFromDataPoint(['value' => 53795, 'timestamp' => '2017-01-01 02:00:00'], 'L'),
            Metric::createFromDataPoint(['value' => 55940, 'timestamp' => '2017-01-01 03:00:00'], 'L'),
        ]);

        $metricPipeline->generate([
            ['value' => 0, 'timestamp' => '2017-01-01 01:00:00'],
            ['value' => 0, 'timestamp' => '2017-01-01 02:00:00'],
            ['value' => 0, 'timestamp' => '2017-01-01 03:00:00'],
        ], 'P')->willReturn([
            Metric::createFromDataPoint(['value' => 0, 'timestamp' => '2017-01-01 01:00:00'], 'P'),
            Metric::createFromDataPoint(['value' => 0, 'timestamp' => '2017-01-01 02:00:00'], 'P'),
            Metric::createFromDataPoint(['value' => 0, 'timestamp' => '2017-01-01 03:00:00'], 'P'),
        ]);


        $this->generate([
            'download' => [
                ['value' => 4589350, 'timestamp' => '2017-01-01 01:00:00'],
                ['value' => 5256140, 'timestamp' => '2017-01-01 02:00:00'],
                ['value' => 3976530, 'timestamp' => '2017-01-01 03:00:00'],
            ],
            'upload' => [
                ['value' => 1158380, 'timestamp' => '2017-01-01 01:00:00'],
                ['value' => 1663000, 'timestamp' => '2017-01-01 02:00:00'],
                ['value' => 2301060, 'timestamp' => '2017-01-01 03:00:00'],
            ],
            'latency' => [
                ['value' => 44856, 'timestamp' => '2017-01-01 01:00:00'],
                ['value' => 53795, 'timestamp' => '2017-01-01 02:00:00'],
                ['value' => 55940, 'timestamp' => '2017-01-01 03:00:00'],
            ],
            'packet_loss' => [
                ['value' => 0, 'timestamp' => '2017-01-01 01:00:00'],
                ['value' => 0, 'timestamp' => '2017-01-01 02:00:00'],
                ['value' => 0, 'timestamp' => '2017-01-01 03:00:00'],
            ],
        ])->shouldGenerate([
            Metric::createFromDataPoint(['value' => 4589350, 'timestamp' => '2017-01-01 01:00:00'], 'D'),
            Metric::createFromDataPoint(['value' => 5256140, 'timestamp' => '2017-01-01 02:00:00'], 'D'),
            Metric::createFromDataPoint(['value' => 3976530, 'timestamp' => '2017-01-01 03:00:00'], 'D'),
            Metric::createFromDataPoint(['value' => 1158380, 'timestamp' => '2017-01-01 01:00:00'], 'U'),
            Metric::createFromDataPoint(['value' => 1663000, 'timestamp' => '2017-01-01 02:00:00'], 'U'),
            Metric::createFromDataPoint(['value' => 2301060, 'timestamp' => '2017-01-01 03:00:00'], 'U'),
            Metric::createFromDataPoint(['value' => 44856, 'timestamp' => '2017-01-01 01:00:00'], 'L'),
            Metric::createFromDataPoint(['value' => 53795, 'timestamp' => '2017-01-01 02:00:00'], 'L'),
            Metric::createFromDataPoint(['value' => 55940, 'timestamp' => '2017-01-01 03:00:00'], 'L'),
            Metric::createFromDataPoint(['value' => 0, 'timestamp' => '2017-01-01 01:00:00'], 'P'),
            Metric::createFromDataPoint(['value' => 0, 'timestamp' => '2017-01-01 02:00:00'], 'P'),
            Metric::createFromDataPoint(['value' => 0, 'timestamp' => '2017-01-01 03:00:00'], 'P'),
        ]);
    }

    /**
     * @todo Remove code duplication.
     */
    public function getMatchers()
    {
        return [
            'generate' => function ($subject, $value) {
                if (!$subject instanceof \Traversable) {
                    throw new FailureException('Return value should be instance of \Traversable');
                }
                $array = iterator_to_array($subject);

                return $array == $value;
            }
        ];
    }
}
