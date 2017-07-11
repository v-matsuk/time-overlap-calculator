<?php

namespace VM\TimeOverlapCalculator\Generator;

use VM\TimeOverlapCalculator\Entity\TimeSlot;

class TimeSlotGenerator implements TimeSlotGeneratorInterface
{
    /**
     * @param \DateTimeInterface $start
     * @param \DateTimeInterface $end
     *
     * @return TimeSlot
     */
    public function createTimeSlot(\DateTimeInterface $start, \DateTimeInterface $end)
    {
        return new TimeSlot($start, $end);
    }
}
