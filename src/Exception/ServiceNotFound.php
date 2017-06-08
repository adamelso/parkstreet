<?php

namespace ParkStreet\Exception;

use PhpSpec\Exception\Exception;
use Psr\Container\NotFoundExceptionInterface;

class ServiceNotFound extends Exception implements NotFoundExceptionInterface
{

}
