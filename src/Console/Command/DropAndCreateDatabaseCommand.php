<?php

namespace ParkStreet\Console\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDOException;
use Doctrine\DBAL\DriverManager;
use ParkStreet\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DropAndCreateDatabaseCommand extends Command
{
    protected function configure()
    {
        $this->setName('db:recreate');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $params = $this->getContainer()->get('database_params');
        $dbname = $params['dbname'];
        unset($params['dbname']);
        $tmpConnection = DriverManager::getConnection($params);

        try {
            $tmpConnection->getSchemaManager()->dropAndCreateDatabase($dbname);
            $output->writeln("Database '{$dbname}' was re-created. Please run `bin/doctrine orm:schema-tool:create` to re-create the schema.");

        } catch (PDOException $e) {
            $tmpConnection->getSchemaManager()->createDatabase($dbname);
            $output->writeln("Database '{$dbname}' was created. Please run `bin/doctrine orm:schema-tool:create` to create the schema.");
        }

        return 0;
    }
}
