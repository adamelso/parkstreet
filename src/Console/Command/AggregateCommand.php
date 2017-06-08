<?php
/**
 * @author    Adam Elsodaney <adam.elsodaney@reiss.com>
 * @date      2017-06-08
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace ParkStreet\Console\Command;


use Doctrine\Common\Persistence\ObjectRepository;
use ParkStreet\Aggregation\MathPhpAggregation;
use ParkStreet\Console\Command;
use ParkStreet\Model\Metric;
use ParkStreet\Model\Unit;
use ParkStreet\Report;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AggregateCommand extends Command
{
    protected function configure()
    {
        $this->setName('aggregate');

        $this->addArgument('metric', InputArgument::REQUIRED);
        $this->addArgument('unit', InputArgument::OPTIONAL, 'Unit ID', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $metricType = $this->getMetricType($input->getArgument('metric'));
        $unitId = $input->getArgument('unit');

        /** @var ObjectRepository $unitRepository */
        $unitRepository = $this->getContainer()->get('repository.unit');

        if (null === $unitId) {
            /** @var Unit[] $units */
            $units = $unitRepository->findAll();
        } else {
            /** @var Unit[] $units */
            $units = $unitRepository->findBy(['unitId' => $unitId]);
        }

        // @todo when count($units) === 0

        $report = new Report\MetricsReport($units, $metricType);

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
    private function getMetricType(string $metricType)
    {
        if (in_array($metricType, $map = [
            'download',
            'upload',
            'latency',
            'packet_loss'
        ])) {
            return $metricType;
        }

        throw new \RuntimeException('Metric type must be one of: ' . implode(', ', array_keys($map)));
    }
}
