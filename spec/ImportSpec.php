<?php

namespace spec\ParkStreet;

use ParkStreet\Client;
use ParkStreet\Feed;
use ParkStreet\Import;
use ParkStreet\Model\Unit;
use ParkStreet\Factory\UnitFactory;
use PhpSpec\Exception\Example\FailureException;
use PhpSpec\ObjectBehavior;
use Psr\Http\Message\StreamInterface;
use Zend\Hydrator\HydrationInterface;

class ImportSpec extends ObjectBehavior
{
    function let(Feed $feed, Client $client, UnitFactory $unitFactory, HydrationInterface $hydrator)
    {
        $this->beConstructedWith($client, $feed, $unitFactory, $hydrator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Import::class);
    }

    function it_imports_the_feed_into_the_domain(Feed $feed, Client $client, StreamInterface $stream, UnitFactory $unitFactory, Unit $unit1, Unit $unit2, HydrationInterface $hydrator)
    {
        $client->connect()->willReturn($stream);

        $feed->process($stream)->willReturn([
            [
                'unit_id' => 1,
            ],
            [
                'unit_id' => 2,
            ]
        ]);

        $unitFactory->createNew()->willReturn($unit1, $unit2);

        $hydrator->hydrate(['unit_id' => 1,], $unit1)->shouldBeCalled();
        $hydrator->hydrate(['unit_id' => 2,], $unit2)->shouldBeCalled();

        $this->run()->shouldGenerate([$unit1, $unit2]);
    }

    public function getMatchers()
    {
        return [
            'generate' => function ($subject, $value) {
                if (!$subject instanceof \Traversable) {
                    throw new FailureException('Return value should be instance of \Traversable');
                }
                $array = iterator_to_array($subject);

                return $array === $value;
            }
        ];
    }
}
