<?php

namespace ParkStreet\Console;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;

class Cli extends Application
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct('Park Street', '0.0.1');

        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }
}
