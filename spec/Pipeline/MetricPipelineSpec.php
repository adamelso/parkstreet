<?php

namespace spec\ParkStreet\Pipeline;

use ParkStreet\Model\Metric;
use ParkStreet\Pipeline\MetricPipeline;
use PhpSpec\Exception\Example\FailureException;
use PhpSpec\ObjectBehavior;

class MetricPipelineSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(MetricPipeline::class);
    }

    function it_returns_a_generator_for_new_metrics_to_import()
    {
        $this->generate([
            ['value' => 100, 'timestamp' => '2017-01-01 01:00:00'],
            ['value' => 0,   'timestamp' => '2017-01-01 02:00:00'],
            ['value' => 15,  'timestamp' => '2017-01-01 03:00:00'],
        ], 'P')->shouldGenerate([
            Metric::createFromDataPoint(['value' => 100, 'timestamp' => '2017-01-01 01:00:00'], 'P'),
            Metric::createFromDataPoint(['value' => 0,   'timestamp' => '2017-01-01 02:00:00'], 'P'),
            Metric::createFromDataPoint(['value' => 15,  'timestamp' => '2017-01-01 03:00:00'], 'P'),
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
