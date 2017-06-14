<?php

namespace ParkStreet\Listener;

use ParkStreet\Event\UnitImported;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Stopwatch\StopwatchEvent;

class MetricImportConsoleSubscriber implements EventSubscriberInterface
{
    const STOPWATCH_IMPORT = 'import';
    /**
     * @var ProgressBar
     */
    private $progressBar;
    /**
     * @var OutputInterface
     */
    private $output;
    /**
     * @var Stopwatch
     */
    private $stopwatch;

    public function __construct(ProgressBar $progressBar, OutputInterface $output, Stopwatch $stopwatch)
    {
        $this->progressBar = $progressBar;
        $this->output = $output;
        $this->stopwatch = $stopwatch;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'metric_import'     => 'onMetricImport',
            'import_start'      => 'onImportStart',
            'import_complete'   => 'onImportComplete',
            UnitImported::EVENT => 'onUnitImport',
        ];
    }

    public function onImportStart()
    {
        $this->progressBar->start();
        $this->stopwatch->start(self::STOPWATCH_IMPORT);
    }

    public function onMetricImport()
    {
        $this->progressBar->advance();
    }

    public function onImportComplete()
    {
        $this->progressBar->finish();
    }

    public function onUnitImport(UnitImported $event)
    {
        $stopwatchEvent = $this->stopwatch->lap(self::STOPWATCH_IMPORT);

        $unit = $event->getUnit();

        $this->output->writeln(sprintf(
            ' | %s | %s | Imported Unit %d with %d metrics.',
            $this->formatDuration($stopwatchEvent),
            $this->formatMemory($stopwatchEvent),

            $unit->getUnitId(),
            $event->getMetricImportCount()
        ));
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
