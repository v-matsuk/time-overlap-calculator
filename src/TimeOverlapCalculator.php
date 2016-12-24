<?php

namespace VM\TimeOverlapCalculator;

use VM\TimeOverlapCalculator\Entity\TimeSlotInterface;
use VM\TimeOverlapCalculator\Generator\TimeSlotGeneratorInterface;

class TimeOverlapCalculator
{
    const TIME_UNIT_SECOND = 1;
    const TIME_UNIT_MINUTE = 60;
    const TIME_UNIT_HOUR = 60 * 60;
    const TIME_UNIT_DAY = 60 * 60 * 24;

    /**
     * Check if given time slots overlap
     *
     * @param TimeSlotInterface $baseTimeSlot
     * @param TimeSlotInterface $overlappingTimeSlot
     *
     * @return bool
     */
    public function isOverlap(TimeSlotInterface $baseTimeSlot, TimeSlotInterface $overlappingTimeSlot)
    {
        return $baseTimeSlot->getStart() < $overlappingTimeSlot->getEnd()
               && $overlappingTimeSlot->getStart() < $baseTimeSlot->getEnd();
    }

    /**
     * Calculate size of overlap and convert into given time unit (seconds, minutes, hours, days)
     *
     * @param TimeSlotInterface $baseTimeSlot
     * @param TimeSlotInterface $overlappingTimeSlot
     * @param int               $timeUnit            self::TIME_UNIT_SECOND by default
     *
     * @return float
     */
    public function calculateOverlap(
        TimeSlotInterface $baseTimeSlot,
        TimeSlotInterface $overlappingTimeSlot,
        $timeUnit = self::TIME_UNIT_SECOND
    ) {
        $overlapSize = 0;

        if ($this->isOverlap($baseTimeSlot, $overlappingTimeSlot)) {
            $overlapStart = ($baseTimeSlot->getStart() < $overlappingTimeSlot->getStart())
                ? $overlappingTimeSlot->getStart()
                : $baseTimeSlot->getStart();

            $overlapEnd = $baseTimeSlot->getEnd() > $overlappingTimeSlot->getEnd()
                ? $overlappingTimeSlot->getEnd()
                : $baseTimeSlot->getEnd();

            $overlapSize = $overlapEnd->getTimestamp() - $overlapStart->getTimestamp();
        }

        return $overlapSize / $timeUnit;
    }

    /**
     * Create an array of non overlapped time slots
     * 
     * @param TimeSlotInterface          $baseTimeSlot
     * @param TimeSlotInterface[]        $overlappingTimeSlots
     * @param TimeSlotGeneratorInterface $timeSlotGenerator
     *
     * @return TimeSlotInterface[]
     */
    public function getNonOverlappedTimeSlots(
        TimeSlotInterface $baseTimeSlot,
        array $overlappingTimeSlots,
        TimeSlotGeneratorInterface $timeSlotGenerator
    ) {
        $baseTimeSlots = [
            $timeSlotGenerator->createTimeSlot($baseTimeSlot->getStart(), $baseTimeSlot->getEnd()),
        ];

        foreach ($overlappingTimeSlots as $overlappingTimeSlot) {
            $freeTimeSlots = [];
            foreach ($baseTimeSlots as $timeSlot) {
                $freeTimeSlots =  array_merge(
                    $freeTimeSlots,
                    $this->fetchNonOverlappedTimeSlots($timeSlot, $overlappingTimeSlot, $timeSlotGenerator)
                );
            }

            $baseTimeSlots = $freeTimeSlots;
        }

        return $baseTimeSlots;
    }

    /**
     * Merge all overlapped time slots into single time slot
     *
     * @param TimeSlotGeneratorInterface $timeSlotGenerator
     * @param TimeSlotInterface[]        $timeSlots
     *
     * @return TimeSlotInterface[]
     */
    public function mergeOverlappedTimeSlots(TimeSlotGeneratorInterface $timeSlotGenerator, array $timeSlots)
    {
        if (empty($timeSlots)) {
            return [];
        }

        $timeSlots = $this->sortTimeSlotsByStartTime($timeSlots);
        $mergedTimeSlots = [
            $timeSlotGenerator->createTimeSlot($timeSlots[0]->getStart(), $timeSlots[0]->getEnd()),
        ];
        $headIndex = 0;

        foreach ($timeSlots as $timeSlot) {
            $headTimeSlot = $mergedTimeSlots[$headIndex];

            if ($timeSlot->getStart() > $headTimeSlot->getEnd()) {
                $mergedTimeSlots[] = $timeSlotGenerator->createTimeSlot(
                    $timeSlot->getStart(),
                    $timeSlot->getEnd()
                );
                $headIndex ++;
            } elseif ($headTimeSlot->getEnd() < $timeSlot->getEnd()) {
                $mergedTimeSlots[$headIndex] = $timeSlotGenerator->createTimeSlot(
                    $headTimeSlot->getStart(),
                    $timeSlot->getEnd()
                );
            }
        }

        return $mergedTimeSlots;
    }

    /**
     * @param TimeSlotInterface          $baseTimeSlot
     * @param TimeSlotInterface          $overlappingTimeSlot
     * @param TimeSlotGeneratorInterface $timeSlotGenerator
     *
     * @return array
     */
    private function fetchNonOverlappedTimeSlots(
        TimeSlotInterface $baseTimeSlot,
        TimeSlotInterface $overlappingTimeSlot,
        TimeSlotGeneratorInterface $timeSlotGenerator
    ) {
        $freeTimeSlots = [];

        if ($this->isOverlap($baseTimeSlot, $overlappingTimeSlot)) {
            if ($overlappingTimeSlot->getStart() > $baseTimeSlot->getStart()
                && $overlappingTimeSlot->getStart() < $baseTimeSlot->getEnd()
            ) {
                $freeTimeSlots[] = $timeSlotGenerator->createTimeSlot(
                    clone $baseTimeSlot->getStart(),
                    clone $overlappingTimeSlot->getStart()
                );
            }

            if ($overlappingTimeSlot->getEnd() > $baseTimeSlot->getStart()
                && $overlappingTimeSlot->getEnd() < $baseTimeSlot->getEnd()
            ) {
                $freeTimeSlots[] = $timeSlotGenerator->createTimeSlot(
                    clone $overlappingTimeSlot->getEnd(),
                    clone $baseTimeSlot->getEnd()
                );
            }
        } else {
            $freeTimeSlots[] = $timeSlotGenerator->createTimeSlot(
                clone $baseTimeSlot->getStart(),
                clone $baseTimeSlot->getEnd()
            );
        }

        return $freeTimeSlots;
    }

    /**
     * @param TimeSlotInterface[] $timeSlots
     *
     * @return TimeSlotInterface[]
     */
    private function sortTimeSlotsByStartTime(array $timeSlots)
    {
        usort($timeSlots, function($a, $b) {
           if ($a->getStart() < $b->getStart()) {
               return -1;
           }

            if ($a->getStart() > $b->getStart()) {
                return 1;
            }

            return 0;
        });

        return $timeSlots;
    }
}
