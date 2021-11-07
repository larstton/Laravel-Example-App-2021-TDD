<?php

namespace Tests\Concerns;

use Carbon\Carbon;
use PHPUnit\Framework\Assert;

trait DateHelpers
{
    /**
     * Compares 2 carbon date instances and are considered equal datetime if they
     * match down to the second, ignoring milliseconds and below.
     *
     * @param  Carbon  $date1
     * @param  Carbon  $date2
     */
    public function assertDateTimesMatch(Carbon $date1, Carbon $date2)
    {
        Assert::assertTrue(
            $date1->floorSecond()->equalTo($date2->floorSecond()),
            'The Carbon instances passed do not match down to the second, ignoring milliseconds and below.'
        );
    }

    public function assertDateTimesDoNotMatch(Carbon $date1, Carbon $date2)
    {
        Assert::assertFalse(
            $date1->floorSecond()->equalTo($date2->floorSecond()),
            'The Carbon instances passed match down to the second, ignoring milliseconds and below.'
        );
    }
}
