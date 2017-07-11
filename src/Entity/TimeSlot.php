<?php

namespace VM\TimeOverlapCalculator\Entity;

class TimeSlot implements TimeSlotInterface
{
    /**
     * @var \DateTimeInterface
     */
    private $start;

    /**
     * @var \DateTimeInterface
     */
    private $end;

    /**
     * @param \DateTimeInterface $start
     * @param \DateTimeInterface $end
     */
    public function __construct(\DateTimeInterface $start, \DateTimeInterface $end)
    {
        $this->start = $start;
        $this->end = $end;

        $this->guard();
    }

    /**
     * @return \DateTimeInterface
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @return \DateTimeInterface
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
