<?php

namespace spec\ParkStreet\Report;

use ParkStreet\Model\Metric;
use ParkStreet\Report\MetricsReport;
use PhpSpec\ObjectBehavior;

class MetricsReportSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('forSingleUnit', [
            42,
            18,
            [27410000, 27630000, 28000000],
            Metric::DOWNLOAD,
        ]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(MetricsReport::class);
    }

    function it_has_a_generated_title()
    {
        $this->getTitle()->shouldBe('Aggregated Download Metrics for Unit 42 at 18:00');
    }

    function it_returns_the_single_row_for_a_table()
    {
        $this->getTableRows()->shouldReturn([
            [
                'Unit Id' => 42,
                'Minimum' => 27410000,
                'Maximum' => 28000000,
                'Mean'    => 27680000,
                'Median'  => 27630000,
            ]
        ]);
    }

    function it_returns_multiple_rows_for_a_table()
    {
        $this->beConstructedThrough('forMultipleUnits', [
            18,
            [
                42 => [27410000, 27630000, 28000000],
                43 => [17410000, 17630000, 18000000],
            ],
            Metric::PACKET_LOSS,
        ]);

        $this->getTitle()->shouldBe('Aggregated Packet Loss Metrics at 18:00');

        $this->getTableRows()->shouldReturn([
            [
                'Unit Id' => 42,
                'Minimum' => 27410000,
                'Maximum' => 28000000,
                'Mean'    => 27680000,
                'Median'  => 27630000,
            ], [
                'Unit Id' => 43,
                'Minimum' => 17410000,
                'Maximum' => 18000000,
                'Mean'    => 17680000,
                'Median'  => 17630000,
            ]
        ]);
    }

    function it_returns_the_table_headers()
    {
        $this->getTableHeaders()->shouldReturn([
            'Unit Id',
            'Minimum',
            'Maximum',
            'Mean',
            'Median',
        ]);
    }
}
