<?php

namespace VM\TimeOverlapCalculator\Entity;

interface TimeSlotInterface
{
    /**
     * @return \DateTime
     */
    public function getStart();

    /**
     * @return \DateTime
     */
    public function getEnd();
}
