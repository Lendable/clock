<?php

declare(strict_types=1);

namespace Lendable\Clock;

/**
 * Simple in-memory fixed clock.
 *
 * The time will not change from the provided value.
 */
final class FixedClock implements MutableClock
{
    private \DateTimeImmutable $now;

    public function __construct(\DateTimeInterface $now)
    {
        $this->now = \DateTimeImmutable::createFromInterface($now);
    }

    public function now(): \DateTimeImmutable
    {
        return $this->now;
    }

    public function nowMutable(): \DateTime
    {
        return \DateTime::createFromImmutable($this->now());
    }

    public function changeTimeTo(\DateTimeInterface $time): void
    {
        $this->now = \DateTimeImmutable::createFromInterface($time);
    }

    public function today(): Date
    {
        return Date::fromDateTime($this->now());
    }
}
