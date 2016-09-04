<?php

namespace VM\TimeOverlapCalculator\Generator;

interface TimeSlotGeneratorInterface
{
    /**
     * @param \DateTime $start
     * @param \DateTime $end
     *
     * @return TimeSlotInterface
     */
    public function createTimeSlot(\DateTime $start, \DateTime $end);
}
