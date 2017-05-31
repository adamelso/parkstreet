<?php

namespace ParkStreet;

use Psr\Http\Message\StreamInterface;

interface Client
{
    public function connect(): StreamInterface;
}
