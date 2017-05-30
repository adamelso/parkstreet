<?php

namespace ParkStreet\Feed;

use ParkStreet\Feed;
use Psr\Http\Message\StreamInterface;

class JsonFeed implements Feed
{
    /**
     * @param StreamInterface $stream
     *
     * @return array
     */
    public function process(StreamInterface $stream)
    {
        return \GuzzleHttp\json_decode($stream->getContents(), true);
    }
}
