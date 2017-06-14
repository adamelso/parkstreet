<?php

namespace ParkStreet\Client;

use ParkStreet\Client;
use GuzzleHttp\Psr7\LazyOpenStream;
use Psr\Http\Message\StreamInterface;

class OfflineClient implements Client
{
    /**
     * @var string
     */
    private $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function connect(): StreamInterface
    {
        return new LazyOpenStream($this->path, 'rb');
    }
}
