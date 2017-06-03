<?php

namespace ParkStreet\Console\Command;

use Doctrine\DBAL\Connection;
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
        /** @var Connection $connection */
        $connection = $this->getContainer()->get('doctrine.connection');

        $dbname = $this->getContainer()->get('database_params')['dbname'];

        $connection->getSchemaManager()->dropAndCreateDatabase($dbname);

        $output->writeln("Database '{$dbname}' was recreated. Please run `bin/doctrine orm:schema-tool:create` to recreate the schema.");

        return 0;
    }
}
