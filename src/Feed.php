<?php

namespace ParkStreet;

use Psr\Http\Message\StreamInterface;

interface Feed
{
    /**
     * @param StreamInterface $stream
     *
     * @return array
     */
    public function process(StreamInterface $stream);
}
