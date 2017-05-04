<?php

use VM\TimeOverlapCalculator\Entity\TimeSlot;

class TimeSlotTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldCreateValidTimeSlot()
    {
        $startDate = new \DateTime('2017-01-01 10:00');
        $endDate = new \DateTime('2017-01-01 11:00');
        $timeSlot = new TimeSlot($startDate, $endDate);

        $this->assertSame($startDate, $timeSlot->getStart());
        $this->assertSame($endDate, $timeSlot->getEnd());
    }

    public function testShouldNotCreateTimeSlotWithInvalidArguments()
    {
        $startDate = new \DateTime('2017-01-01 10:00');
        $endDate = new \DateTime('2017-01-01 09:00');

        $this->setExpectedException(\Exception::class);

        new TimeSlot($startDate, $endDate);
    }

    public function testShouldNotCreateTimeSlotIfStartEqualsToEndDate()
    {
        $startDate = new \DateTime('2017-01-01 10:00');
        $endDate = new \DateTime('2017-01-01 10:00');

        $this->setExpectedException(\Exception::class);

        new TimeSlot($startDate, $endDate);
    }
}
