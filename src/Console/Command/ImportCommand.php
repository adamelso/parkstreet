<?php

namespace ParkStreet\Console\Command;

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

        $n = 0;

        foreach ($import->run() as $unit) {
            $output->writeln(sprintf('Unit %d was imported.', $unit->getUnitId()));

            ++$n;
        }

        $output->writeln(0 === $n ? '<comment>Nothing to import.</comment>' : sprintf('<info>Imported %d items.</info>', $n));

        return 0;
    }
}
