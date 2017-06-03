<?php

namespace ParkStreet\Console\Command;

use Doctrine\DBAL\Connection;
use ParkStreet\Console\Command;
use ParkStreet\Import;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCommand extends Command
{
    protected function configure()
    {
        $this->setName('import');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Import $import */
        $import = $this->getContainer()->get('import');

        foreach ($import->run() as $unit) {
            $output->writeln(sprintf('Unit %d was imported.', $unit->getUnitId()));
        }

        return 0;
    }
}