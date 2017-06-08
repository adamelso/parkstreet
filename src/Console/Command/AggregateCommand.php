<?php

namespace ParkStreet\Console\Command;

use Doctrine\Common\Util\Debug;
use ParkStreet\Console\Command;
use ParkStreet\Model\Metric;
use ParkStreet\Model\Unit;
use ParkStreet\Report;
use ParkStreet\Repository\MetricRepository;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AggregateCommand extends Command
{
    protected function configure()
    {
        $this->setName('aggregate');

        $this->addArgument('metric', InputArgument::REQUIRED);
        $this->addArgument('hour', InputArgument::REQUIRED);
        $this->addOption('unit', 'u', InputArgument::OPTIONAL, 'Unit ID', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $metricTypeCode = $this->getMetricTypeCode($input->getArgument('metric'));
        $hour = $input->getArgument('hour');
        $unitId = $input->getOption('unit');

        /** @var MetricRepository $metricRepository */
        $metricRepository = $this->getContainer()->get('repository.metric');


        if ($unitId) {
            $metrics = $metricRepository->selectUnitDataPointsByHour($unitId, $metricTypeCode, $hour);
            $report = new Report\MetricsReport([$unitId => $metrics], $metricTypeCode);
        } else {
            $metricsByUnit = $metricRepository->selectAllUnitsDataPointsByHour($metricTypeCode, $hour);
            $report = new Report\MetricsReport($metricsByUnit, $metricTypeCode);
        }

        $io = new SymfonyStyle($input, $output);

        $io->title($report->getTitle());
        $io->table(
            ['Unit ID', 'Min', 'Max', 'Mean', 'Median'],
            $report->getTableRows()
        );

        return 0;
    }

    /**
     * @param string $metricType
     *
     * @return string
     *
     * @throws \RuntimeException if metric type is unrecognized.
     */
    private function getMetricTypeCode(string $metricType)
    {
        $map = [
            'download'    => Metric::DOWNLOAD,
            'upload'      => Metric::UPLOAD,
            'latency'     => Metric::LATENCY,
            'packet_loss' => Metric::PACKET_LOSS,
        ];

        if (isset($map[$metricType])) {
            return $map[$metricType];
        }

        throw new \RuntimeException('Metric type must be one of: ' . implode(', ', array_keys($map)));
    }

}
