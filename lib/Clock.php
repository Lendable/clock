<?php

declare(strict_types=1);

namespace Lendable\Clock;

use Psr\Clock\ClockInterface;

interface Clock extends ClockInterface
{
    /**
     * Provides the current system time.
     */
    public function now(): \DateTimeImmutable;

    /**
     * Provides the current system time as a mutable instance.
     *
     * The instance provided is not the internal instance used by the clock.
     * Mutations made to the instance will not be reflected in the clock to
     * prevent unintended side effects.
     */
    public function nowMutable(): \DateTime;

    public function today(): Date;
}
