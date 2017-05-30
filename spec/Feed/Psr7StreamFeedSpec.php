<?php

namespace spec\ParkStreet\Feed;

use ParkStreet\Feed\Psr7StreamFeed;
use PhpSpec\ObjectBehavior;
use Psr\Http\Message\StreamInterface;

class Psr7StreamFeedSpec extends ObjectBehavior
{
    function let(StreamInterface $stream)
    {
        $this->beConstructedWith($stream);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Psr7StreamFeed::class);
    }

    function it_is_a_feed()
    {
        $this->shouldImplement(\ParkStreet\Feed::class);
    }

    function it_decodes_JSON_from_the_contents_of_a_PSR_7_stream(StreamInterface $stream)
    {
        $stream->getContents()->willReturn(<<<JSON
[
  {
    "unit_id": 1
  },
  {
    "unit_id": 2
  }
]
JSON
);

        $this->process()->shouldReturn([
            [
                'unit_id' => 1,
            ],
            [
                'unit_id' => 2,
            ]
        ]);
    }
}
