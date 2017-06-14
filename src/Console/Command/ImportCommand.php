<?php

namespace ParkStreet\Console\Command;

use ParkStreet\Console\Command;
use ParkStreet\ImportRunner;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCommand extends Command
{
    protected function configure()
    {
        $this->setName('import');
        $this->addOption('live', 'l', InputOption::VALUE_NONE, 'Connect to a live feed instead of using the offline one.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var ImportRunner $import */
        $import = $this->getContainer()->get('import');

        if ($input->getOption('live')) {
            $import->setClient($this->getContainer()->get('client.live'));
        }

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
