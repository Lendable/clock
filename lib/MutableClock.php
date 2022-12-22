<?php

declare(strict_types=1);

namespace Lendable\Clock;

interface MutableClock extends Clock
{
    public function changeTimeTo(\DateTimeInterface $time): void;

    public function rewindTimeBy(\DateInterval $interval): void;

    public function advanceTimeBy(\DateInterval $interval): void;
}
