<?php

namespace spec\ParkStreet;

use Doctrine\Common\Persistence\ObjectRepository;
use ParkStreet\Client;
use ParkStreet\Event\BatchImported;
use ParkStreet\Event\UnitImported;
use ParkStreet\Feed;
use ParkStreet\ImportRunner;
use ParkStreet\Model\Metric;
use ParkStreet\Model\Unit;
use ParkStreet\Pipeline\UnitPipeline;
use PhpSpec\Exception\Example\FailureException;
use PhpSpec\ObjectBehavior;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ImportRunnerSpec extends ObjectBehavior
{
    function let(Client $client, Feed $feed, UnitPipeline $unitPipeline, ObjectRepository $unitRepository, EventDispatcherInterface $eventDispatcher)
    {
        $this->beConstructedWith($client, $feed, $unitPipeline, $unitRepository, $eventDispatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ImportRunner::class);
    }

    function it_imports_the_feed_into_existing_units(Client $client, Feed $feed, EventDispatcherInterface $eventDispatcher, UnitPipeline $unitPipeline, ObjectRepository $unitRepository, StreamInterface $stream, Unit $unit1, Unit $unit2, Metric $unit1Download, Metric $unit1Upload, Metric $unit1Latency, Metric $unit1PacketLoss, Metric $unit2Download, Metric $unit2Upload,  Metric $unit2Latency, Metric $unit2PacketLoss)
    {
        $client->connect()->willReturn($stream);
        $feed->process($stream)->willReturn([
            [
                'unit_id' => 1,
                'metrics' => [
                    'download' => [
                        ['value' => 4589350, 'timestamp' => '2017-01-01 01:00:00'],
                    ],
                    'upload' => [
                        ['value' => 1158380, 'timestamp' => '2017-01-01 01:00:00'],
                    ],
                    'latency' => [
                        ['value' => 44856, 'timestamp' => '2017-01-01 01:00:00'],
                    ],
                    'packet_loss' => [
                        ['value' => 0, 'timestamp' => '2017-01-01 01:00:00'],
                    ],
                ],
            ],
            [
                'unit_id' => 2,
                'metrics' => [
                    'download' => [
                        ['value' => 3976530, 'timestamp' => '2017-01-01 03:00:00'],
                    ],
                    'upload' => [
                        ['value' => 2301060, 'timestamp' => '2017-01-01 03:00:00'],
                    ],
                    'latency' => [
                        ['value' => 55940, 'timestamp' => '2017-01-01 03:00:00'],
                    ],
                    'packet_loss' => [
                        ['value' => 0, 'timestamp' => '2017-01-01 03:00:00'],
                    ],
                ],
            ]
        ]);


        $unitRepository->findOneBy(['unitId' => 1])->willReturn($unit1);
        $unitRepository->findOneBy(['unitId' => 2])->willReturn($unit2);

        $unitPipeline->generate([
            'download' => [
                ['value' => 4589350, 'timestamp' => '2017-01-01 01:00:00'],
            ],
            'upload' => [
                ['value' => 1158380, 'timestamp' => '2017-01-01 01:00:00'],
            ],
            'latency' => [
                ['value' => 44856, 'timestamp' => '2017-01-01 01:00:00'],
            ],
            'packet_loss' => [
                ['value' => 0, 'timestamp' => '2017-01-01 01:00:00'],
            ],
        ])->willReturn([$unit1Download, $unit1Upload, $unit1Latency, $unit1PacketLoss]);

        $unitPipeline->generate([
            'download' => [
                ['value' => 3976530, 'timestamp' => '2017-01-01 03:00:00'],
            ],
            'upload' => [
                ['value' => 2301060, 'timestamp' => '2017-01-01 03:00:00'],
            ],
            'latency' => [
                ['value' => 55940, 'timestamp' => '2017-01-01 03:00:00'],
            ],
            'packet_loss' => [
                ['value' => 0, 'timestamp' => '2017-01-01 03:00:00'],
            ],
        ])->willReturn([$unit2Download, $unit2Upload, $unit2Latency, $unit2PacketLoss]);

        $unit1->addMetric($unit1Download)->shouldBeCalled();
        $unit1->addMetric($unit1Upload)->shouldBeCalled();
        $unit1->addMetric($unit1Latency)->shouldBeCalled();
        $unit1->addMetric($unit1PacketLoss)->shouldBeCalled();

        $unit2->addMetric($unit2Download)->shouldBeCalled();
        $unit2->addMetric($unit2Upload)->shouldBeCalled();
        $unit2->addMetric($unit2Latency)->shouldBeCalled();
        $unit2->addMetric($unit2PacketLoss)->shouldBeCalled();

        // @todo Consistent event naming.
        $eventDispatcher->dispatch('import_start')->shouldBeCalled();
        $eventDispatcher->dispatch('metric_import')->shouldBeCalledTimes(8);
        $eventDispatcher->dispatch('import_batch_complete', new BatchImported($unit1->getWrappedObject()))->shouldBeCalled();
        $eventDispatcher->dispatch('import_batch_complete', new BatchImported($unit2->getWrappedObject()))->shouldBeCalled();
        $eventDispatcher->dispatch('import_unit_complete', new UnitImported($unit1->getWrappedObject()))->shouldBeCalled();
        $eventDispatcher->dispatch('import_unit_complete', new UnitImported($unit2->getWrappedObject()))->shouldBeCalled();
        $eventDispatcher->dispatch('import_complete')->shouldBeCalled();

        $this->run(3)->shouldGenerate([$unit1, $unit2]);
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
