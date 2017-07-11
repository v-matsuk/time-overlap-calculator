# Time overlap calculator
=========================

[![Build Status](https://travis-ci.org/v-matsuk/time-overlap-calculator.svg?branch=master)](https://travis-ci.org/v-matsuk/time-overlap-calculator)
[![codecov](https://codecov.io/gh/v-matsuk/time-overlap-calculator/branch/master/graph/badge.svg)](https://codecov.io/gh/v-matsuk/time-overlap-calculator)

A lightweight library that helps to work with date/time overlapping.

## Installation
```bash
$ composer require v-matsuk/time-overlap-calculator
```

## Usage
```php
<?php

use VM\TimeOverlapCalculator\TimeOverlapCalculator;
use VM\TimeOverlapCalculator\Entity\TimeSlot;
use VM\TimeOverlapCalculator\Generator\TimeSlotGenerator;

$calculator = new TimeOverlapCalculator();

$baseTimeSlot = new TimeSlot(
    new \DateTime('2016-01-01 08:00'),
    new \DateTime('2016-01-01 20:00')
);
$overlappingTimeSlot = new TimeSlot(
   new \DateTime('2016-01-01 13:00'),
   new \DateTime('2016-01-01 17:00')
);
```
### Check if two periods overlap
```php
$isOverlap = $calculator->isOverlap($baseTimeSlot, $overlappingTimeSlot); //will return true
```
### Calculate size of overlapping and convert result into given time unit (seconds by default)
```php
$resultInSeconds = $calculator->calculateOverlap($baseTimeSlot, $overlappingTimeSlot); //14400
$resultInMinutes = $calculator->calculateOverlap($baseTimeSlot, $overlappingTimeSlot, TimeOverlapCalculator::TIME_UNIT_MINUTE); //240
$resultInHours = $calculator->calculateOverlap($baseTimeSlot, $overlappingTimeSlot, TimeOverlapCalculator::TIME_UNIT_HOUR); //4
```
### Generate an array of non-overlapped time slots
```php
//will return array that contains two time slots:
//from 2016-01-01 08:00 till 2016-01-01 13:00 and from 2016-01-01 17:00 till 2016-01-01 20:00
$timeSlotGenerator = new TimeSlotGenerator();
$freeTimeSlots = $calculator->getNonOverlappedTimeSlots(
    $baseTimeSlot,
    [$overlappingTimeSlot],
    $timeSlotGenerator
);
```
TimeSlotGenerator is used to generate new time slots that appear after exclusion of all overlapping time slots from base time slot.

### Merge overlapped time slots into single time slot
```php
//will return array that contains two time slots:
//from 2016-01-01 10:00 till 2016-01-01 16:00 and from 2016-01-01 19:00 till 2016-01-01 22:00
$timeSlotGenerator = new TimeSlotGenerator();
$freeTimeSlots = $calculator->mergeOverlappedTimeSlots(
    $timeSlotGenerator,
    [
        new TimeSlot(new \DateTime('2016-01-01 13:00'), new \DateTime('2016-01-01 16:00')),
        new TimeSlot(new \DateTime('2016-01-01 11:00'), new \DateTime('2016-01-01 14:00')),
        new TimeSlot(new \DateTime('2016-01-01 19:00'), new \DateTime('2016-01-01 22:00')),
        new TimeSlot(new \DateTime('2016-01-01 10:00'), new \DateTime('2016-01-01 13:00')),
    ]
);
```
TimeSlotGenerator is used to generate new time slots that appear after merging of all overlapping time slots.

You can use your own implementation of TimeSlot. Your class should implement TimeSlotInterface.
Also you can use custom TimeSlotGenerator. Your class should implement TimeSlotGeneratorInterface.
