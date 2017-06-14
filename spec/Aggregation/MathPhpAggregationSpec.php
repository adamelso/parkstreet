<?php

namespace spec\ParkStreet\Aggregation;

use ParkStreet\Aggregation\MathPhpAggregation;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MathPhpAggregationSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith([150, 350, 100, 500, 450]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(MathPhpAggregation::class);
    }

    function it_calculates_the_mean()
    {
        $this->mean()->shouldEqual(310);
    }

    function it_calculates_the_median()
    {
        $this->median()->shouldEqual(350);
    }

    function it_calculates_the_minimum()
    {
        $this->minimum()->shouldEqual(100);
    }

    function it_calculates_the_maximum()
    {
        $this->maximum()->shouldEqual(500);
    }

    function it_returns_the_mean_of_the_median_2_items_as_the_median_if_there_are_an_even_number_of_items()
    {
        $this->beConstructedWith([100, 295, 305, 400]);

        $this->mean()->shouldEqual(275);
        $this->median()->shouldEqual(300);
    }
}
