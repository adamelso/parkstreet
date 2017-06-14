<?php


namespace ParkStreet\Client;

use GuzzleHttp\Psr7\CachingStream;
use ParkStreet\Client;
use Psr\Http\Message\StreamInterface;

/**
 * @todo Consider using GuzzleHttp\Client
 */
class LiveClient implements Client
{
    /**
     * @var
     */
    private $uri;

    public function __construct($uri)
    {
        $this->uri = $uri;
    }

    public function connect(): StreamInterface
    {
        $original = \GuzzleHttp\Psr7\stream_for(fopen($this->uri, 'rb'));

        return new CachingStream($original);
    }
}
