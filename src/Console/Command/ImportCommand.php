<?php

namespace ParkStreet\Console\Command;

use ParkStreet\Console\Command;
use ParkStreet\ImportRunner;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Stopwatch\StopwatchEvent;

class ImportCommand extends Command
{
    protected function configure()
    {
        $this->setName('import');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var ImportRunner $import */
        $import = $this->getContainer()->get('import');

        $stopwatch = new Stopwatch();

        $stopwatch->start('import');

        $n = 0;

        foreach ($import->run() as $unit) {
            ++$n;
        }

        $output->writeln(0 === $n
            ? '<comment>Nothing to import.</comment>'
            : sprintf(' <info>Imported %d items.</info>', $n)
        );

        return 0;
    }
}
