<?php

namespace VM\TimeOverlapCalculator\Generator;

interface TimeSlotGeneratorInterface
{
    /**
     * @param \DateTimeInterface $start
     * @param \DateTimeInterface $end
     *
     * @return TimeSlotInterface
     */
    public function createTimeSlot(\DateTimeInterface $start, \DateTimeInterface $end);
}
