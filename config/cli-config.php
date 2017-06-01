<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = require __DIR__.'/container.php';

/** @var EntityManager $entityManager */
$entityManager = $container->get('doctrine.object_manager');

return ConsoleRunner::createHelperSet($entityManager);
