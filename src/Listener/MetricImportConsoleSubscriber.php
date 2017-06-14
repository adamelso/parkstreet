<?php

namespace ParkStreet\Listener;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MetricImportConsoleSubscriber implements EventSubscriberInterface
{
    /**
     * @var ProgressBar
     */
    private $progressBar;

    public function __construct(ProgressBar $progressBar)
    {
        $this->progressBar = $progressBar;
    }

    public function onImportStart()
    {
        $this->progressBar->start();
    }

    public function onMetricImport()
    {
        $this->progressBar->advance();
    }

    public function onImportComplete()
    {
        $this->progressBar->finish();
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'metric_import'   => 'onMetricImport',
            'import_start'    => 'onImportStart',
            'import_complete' => 'onImportComplete',
        ];
    }
}
