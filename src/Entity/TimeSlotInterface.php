<?php

namespace VM\TimeOverlapCalculator\Entity;

interface TimeSlotInterface
{
    /**
     * @return \DateTimeInterface
     */
    public function getStart();

    /**
     * @return \DateTimeInterface
     */
    public function getEnd();
}
