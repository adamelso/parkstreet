<?php

namespace spec\ParkStreet\Feed;

use ParkStreet\Feed\JsonFeed;
use PhpSpec\ObjectBehavior;
use Psr\Http\Message\StreamInterface;

class JsonFeedSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(JsonFeed::class);
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

        $this->process($stream)->shouldReturn([
            [
                'unit_id' => 1,
            ],
            [
                'unit_id' => 2,
            ]
        ]);
    }
}
