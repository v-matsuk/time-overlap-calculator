<?php

use VM\TimeOverlapCalculator\Entity\TimeSlot;
use VM\TimeOverlapCalculator\Generator\TimeSlotGenerator;

class TimeSlotGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldCreateValidTimeSlot()
    {
        $startDate = new \DateTime('2017-01-01 10:00');
        $endDate = new \DateTime('2017-01-01 11:00');
        $timeSlotGenerator = new TimeSlotGenerator();
        $timeSLot = $timeSlotGenerator->createTimeSlot($startDate, $endDate);

        $this->assertInstanceOf(TimeSlot::class, $timeSLot);
    }
}
