<?php

declare(strict_types=1);

namespace Lendable\Clock;

interface ClockProvider
{
    public function getClock(): Clock;
}
