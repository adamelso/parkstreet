<?php

namespace ParkStreet\Client;

use ParkStreet\Client;
use GuzzleHttp\Psr7\LazyOpenStream;
use Psr\Http\Message\StreamInterface;

class OfflineClient implements Client
{
    public function connect(): StreamInterface
    {
        return new LazyOpenStream(__DIR__.'/../../testdata.json', 'r');
    }
}
