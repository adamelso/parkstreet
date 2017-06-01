<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Repository\DefaultRepositoryFactory;
use Doctrine\ORM\Tools\Setup;
use ParkStreet\Model\Unit;
use Pimple\Container;

$container = new Container();

$container['debug'] = false;

$container['database_params'] = [
    'driver'   => 'pdo_mysql',
    'user'     => 'root',
    'password' => '',
    'dbname'   => 'park_street',
];

$container['doctrine.config.annotations'] = (function (Container $c) {
    AnnotationRegistry::registerFile(__DIR__."/../vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php");

    return Setup::createAnnotationMetadataConfiguration([__DIR__. '/../src/Model'], $c['debug'], null, null, false);
});

$container['doctrine.object_manager'] = (function (Container $c) {
    return EntityManager::create($c['database_params'], $c['doctrine.config.annotations']);
});

$container['doctrine.repository_factory'] = (function () {
    return new DefaultRepositoryFactory();
});

$container['repository.unit'] = (function (Container $c) {
    /** @var DefaultRepositoryFactory $factory */
    $factory = $c['doctrine.repository_factory'];

    return $factory->getRepository($c['doctrine.object_manager'], Unit::class);
});

// Pimple is not yet PSR-11 compliant because of method name collision in Silex, so this
// anonymous class wraps PSR-11 compatibility around the Pimple container.
return new class ($container) implements \Psr\Container\ContainerInterface {

    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws \Psr\Container\NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws \Psr\Container\ContainerExceptionInterface Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get($id)
    {
        if (isset($this->container[$id])) {
            return $this->container[$id];
        }

        throw new class ("Service '{$id}' not found") implements \Psr\Container\NotFoundExceptionInterface {};
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($id)
    {
        return isset($this->container[$id]);
    }
};
