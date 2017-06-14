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
            $stopwatchEvent = $stopwatch->lap('import');

            $output->writeln(sprintf(
                ' Unit %d was imported | %s | %s',
                $unit->getUnitId(),
                $this->formatDuration($stopwatchEvent),
                $this->formatMemory($stopwatchEvent)
            ));

            ++$n;
        }

        $output->writeln(0 === $n
            ? '<comment>Nothing to import.</comment>'
            : sprintf('<info>Imported %d items.</info>', $n)
        );

        return 0;
    }

    /**
     * @param StopwatchEvent $stopwatchEvent
     * @return string
     */
    private function formatDuration(StopwatchEvent $stopwatchEvent)
    {
        return bcdiv($stopwatchEvent->getDuration(), 1000, 0) . ' secs';
    }

    private function formatMemory(StopwatchEvent $stopwatchEvent)
    {
        $memory = $stopwatchEvent->getMemory();
        $power = (int) log($memory, 1024);

        return $memory / 1024 ** $power.' '. ['B', 'KiB', 'MiB', 'GiB'][$power];
    }
}
