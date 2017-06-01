<?php

namespace ParkStreet\Console;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class Command extends SymfonyCommand
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        if (! $this->container) {
            /** @var Cli $application */
            $application = $this->getApplication();
            $this->container = $application->getContainer();
        }

        return $this->container;
    }
}
