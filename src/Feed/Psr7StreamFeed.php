<?php

namespace ParkStreet\Feed;

use ParkStreet\Feed;
use Psr\Http\Message\StreamInterface;

class Psr7StreamFeed implements Feed
{
    /**
     * @var StreamInterface
     */
    private $stream;

    public function __construct(StreamInterface $stream)
    {
        $this->stream = $stream;
    }

    public function process()
    {
        return \GuzzleHttp\json_decode($this->stream->getContents(), true);
    }
}
