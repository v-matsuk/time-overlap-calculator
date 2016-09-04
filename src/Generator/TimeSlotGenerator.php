<?php

namespace VM\TimeOverlapCalculator\Generator;

use VM\TimeOverlapCalculator\Entity\TimeSlot;

class TimeSlotGenerator implements TimeSlotGeneratorInterface
{
    /**
     * @param \DateTime $start
     * @param \DateTime $end
     *
     * @return TimeSlot
     */
    public function createTimeSlot(\DateTime $start, \DateTime $end)
    {
        return new TimeSlot($start, $end);
    }
}
