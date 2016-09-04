<?php

namespace VM\TimeOverlapCalculator\Entity;

class TimeSlot implements TimeSlotInterface
{
    /**
     * @var \DateTime
     */
    private $start;

    /**
     * @var \DateTime
     */
    private $end;

    /**
     * @param \DateTime $start
     * @param \DateTime $end
     */
    public function __construct(\DateTime $start, \DateTime $end)
    {
        $this->start = $start;
        $this->end = $end;

        $this->guard();
    }

    /**
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @throws \Exception
     */
    private function guard()
    {
        if ($this->start >= $this->end) {
            throw new \Exception('Time slot start time should be less than time slot end time');
        }
    }
}
