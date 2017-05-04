<?php

use VM\TimeOverlapCalculator\TimeOverlapCalculator;
use VM\TimeOverlapCalculator\Entity\TimeSlot;
use VM\TimeOverlapCalculator\Generator\TimeSlotGenerator;

class TimeOverlapCalculatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TimeOverlapCalculator
     */
    private $calculator;

    public function tearDown()
    {
        $this->calculator = null;
    }

    public function setUp()
    {
        $this->calculator = new TimeOverlapCalculator();
    }

    /**
     * Tests for TimeOverlapCalculator::isOverlap()
     */
    public function testIsOverlap()
    {
        $baseTimeSlot = new TimeSlot(new \DateTime('2016-09-01 10:00'), new \DateTime('2016-09-01 17:00'));
        //the second period is before the first
        $result = $this->calculator->isOverlap(
            $baseTimeSlot,
            new TimeSlot(new \DateTime('2016-09-01 08:00'), new \DateTime('2016-09-01 10:00'))
        );
        $this->assertFalse($result);

        //the second period is after the first
        $result = $this->calculator->isOverlap(
            $baseTimeSlot,
            new TimeSlot(new \DateTime('2016-09-01 17:00'), new \DateTime('2016-09-01 20:00'))
        );
        $this->assertFalse($result);

        //the second period overlap the first one from left side
        $result = $this->calculator->isOverlap(
            $baseTimeSlot,
            new TimeSlot(new \DateTime('2016-09-01 09:00'), new \DateTime('2016-09-01 12:00'))
        );
        $this->assertTrue($result);

        //the second period overlap the first one from right side
        $result = $this->calculator->isOverlap(
            $baseTimeSlot,
            new TimeSlot(new \DateTime('2016-09-01 14:00'), new \DateTime('2016-09-01 19:00'))
        );
        $this->assertTrue($result);

        //the second period is inside the first one
        $result = $this->calculator->isOverlap(
            $baseTimeSlot,
            new TimeSlot(new \DateTime('2016-09-01 13:00'), new \DateTime('2016-09-01 16:00'))
        );
        $this->assertTrue($result);

        //the first period is inside the second one
        $result = $this->calculator->isOverlap(
            $baseTimeSlot,
            new TimeSlot(new \DateTime('2016-09-01 08:00'), new \DateTime('2016-09-01 20:00'))
        );
        $this->assertTrue($result);
    }

    /**
     * Tests for TimeOverlapCalculator::calculateOverlap()
     */
    public function testCalculateOverlap()
    {
        $baseTimeSlot = new TimeSlot(new \DateTime('2016-09-01 10:00'), new \DateTime('2016-09-01 17:00'));
        //the second period is before the first
        $result = $this->calculator->calculateOverlap(
            $baseTimeSlot,
            new TimeSlot(new \DateTime('2016-09-01 08:00'), new \DateTime('2016-09-01 10:00'))
        );
        $this->assertEquals(0, $result);

        //the second period is after the first
        $result = $this->calculator->calculateOverlap(
            $baseTimeSlot,
            new TimeSlot(new \DateTime('2016-09-01 17:00'), new \DateTime('2016-09-01 20:00'))
        );
        $this->assertEquals(0, $result);

        //the second period overlap the first from left side
        $result = $this->calculator->calculateOverlap(
            $baseTimeSlot,
            new TimeSlot(new \DateTime('2016-09-01 09:00'), new \DateTime('2016-09-01 12:00'))
        );
        $this->assertEquals(2 * 60 * 60, $result);

        //the second period overlap the first from right side
        $result = $this->calculator->calculateOverlap(
            $baseTimeSlot,
            new TimeSlot(new \DateTime('2016-09-01 14:00'), new \DateTime('2016-09-01 19:00'))
        );
        $this->assertEquals(3 * 60 * 60, $result);

        //the second period is inside the first
        $result = $this->calculator->calculateOverlap(
            $baseTimeSlot,
            new TimeSlot(new \DateTime('2016-09-01 12:00'), new \DateTime('2016-09-01 16:00'))
        );
        $this->assertEquals(4 * 60 * 60, $result);

        //the first period is inside the second
        $result = $this->calculator->calculateOverlap(
            $baseTimeSlot,
            new TimeSlot(new \DateTime('2016-09-01 08:00'), new \DateTime('2016-09-01 20:00'))
        );
        $this->assertEquals(7 * 60 * 60, $result);

        //the first period is inside the second (minutes)
        $result = $this->calculator->calculateOverlap(
            $baseTimeSlot,
            new TimeSlot(new \DateTime('2016-09-01 08:00'), new \DateTime('2016-09-01 20:00')),
            TimeOverlapCalculator::TIME_UNIT_MINUTE
        );
        $this->assertEquals(7 * 60, $result);

        //the first period is inside the second (hours)
        $result = $this->calculator->calculateOverlap(
            $baseTimeSlot,
            new TimeSlot(new \DateTime('2016-09-01 08:00'), new \DateTime('2016-09-01 20:00')),
            TimeOverlapCalculator::TIME_UNIT_HOUR
        );
        $this->assertEquals(7, $result);
    }

    /**
     * Tests for TimeOverlapCalculator::getNonOverlappedTimeSlots()
     */
    public function testGetNonOverlappedTimeSlots()
    {
        $baseTimeSlot = new TimeSlot(new \DateTime('2016-09-01 10:00'), new \DateTime('2016-09-01 17:00'));
        $overlappingTimeSlots = [
            new TimeSlot(new \DateTime('2016-09-01 09:00'), new \DateTime('2016-09-01 10:00')),
            new TimeSlot(new \DateTime('2016-09-01 09:00'), new \DateTime('2016-09-01 11:00')),
            new TimeSlot(new \DateTime('2016-09-01 13:00'), new \DateTime('2016-09-01 14:00')),
            new TimeSlot(new \DateTime('2016-09-01 13:30'), new \DateTime('2016-09-01 15:00')),
            new TimeSlot(new \DateTime('2016-09-01 16:30'), new \DateTime('2016-09-01 17:00')),
            new TimeSlot(new \DateTime('2016-09-01 17:00'), new \DateTime('2016-09-01 20:00')),
        ];
        $timeSlotGenerator = new TimeSlotGenerator();

        $result = $this->calculator->getNonOverlappedTimeSlots(
            $baseTimeSlot,
            $overlappingTimeSlots,
            $timeSlotGenerator
        );

        $this->assertCount(2, $result);
        $this->assertInstanceOf(TimeSlot::class, $result[0]);
        $this->assertEquals('2016-09-01 11:00', $result[0]->getStart()->format('Y-m-d H:i'));
        $this->assertEquals('2016-09-01 13:00', $result[0]->getEnd()->format('Y-m-d H:i'));

        $this->assertInstanceOf(TimeSlot::class, $result[1]);
        $this->assertEquals('2016-09-01 15:00', $result[1]->getStart()->format('Y-m-d H:i'));
        $this->assertEquals('2016-09-01 16:30', $result[1]->getEnd()->format('Y-m-d H:i'));
    }

    /**
     * Tests for TimeOverlapCalculator::mergeOverlappedTimeSlots()
     */
    public function testMergeTimeSlots()
    {
        $timeSlotGenerator = new TimeSlotGenerator();

        $mergedTimeSlots = $this->calculator->mergeOverlappedTimeSlots(
            $timeSlotGenerator,
            [
                new TimeSlot(new \DateTime('2016-01-01 13:00'), new \DateTime('2016-01-01 16:00')),
                new TimeSlot(new \DateTime('2016-01-01 11:00'), new \DateTime('2016-01-01 12:00')),
                new TimeSlot(new \DateTime('2016-01-01 19:00'), new \DateTime('2016-01-01 22:00')),
                new TimeSlot(new \DateTime('2016-01-01 10:00'), new \DateTime('2016-01-01 13:00')),
            ]
        );

        $this->assertCount(2, $mergedTimeSlots);
        $this->assertInstanceOf(TimeSlot::class, $mergedTimeSlots[0]);
        $this->assertEquals('2016-01-01 10:00', $mergedTimeSlots[0]->getStart()->format('Y-m-d H:i'));
        $this->assertEquals('2016-01-01 16:00', $mergedTimeSlots[0]->getEnd()->format('Y-m-d H:i'));

        $this->assertInstanceOf(TimeSlot::class, $mergedTimeSlots[1]);
        $this->assertEquals('2016-01-01 19:00', $mergedTimeSlots[1]->getStart()->format('Y-m-d H:i'));
        $this->assertEquals('2016-01-01 22:00', $mergedTimeSlots[1]->getEnd()->format('Y-m-d H:i'));
    }
}