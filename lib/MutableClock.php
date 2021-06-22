<?php

declare(strict_types=1);

namespace Lendable\Clock;

interface MutableClock extends Clock
{
    public function changeTimeTo(\DateTimeInterface $time): void;
}
