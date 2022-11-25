<?php

declare(strict_types=1);

namespace Lendable\Clock;

/**
 * Simple in-memory ticking mock clock.
 *
 * The original time value will advance forward as normal to simulate time passing from
 * that time point.
 */
final class TickingMockClock implements MutableClock
{
    private \DateTimeImmutable $now;

    private function __construct(\DateTimeInterface $now, private \DateTimeImmutable $tickingFrom)
    {
        $this->now = \DateTimeImmutable::createFromInterface($now);
    }

    public static function tickingFromCurrentTime(\DateTimeImmutable $now): self
    {
        return self::tickingFromTime($now, new \DateTimeImmutable('now'));
    }

    /**
     * Constructs with a specific point in time the clock was originally began ticking from.
     *
     * This allows for testing where you are persisting state of a clock across in-memory boundaries
     * and need to re-hydrate into that state, rather than begin ticking time from the current time again.
     */
    public static function tickingFromTime(\DateTimeImmutable $now, \DateTimeImmutable $tickingFrom): self
    {
        return new self($now, $tickingFrom);
    }

    public function now(): \DateTimeImmutable
    {
        return $this->now->add($this->calculateOffset());
    }

    public function nowMutable(): \DateTime
    {
        return \DateTime::createFromImmutable($this->now());
    }

    public function changeTimeTo(\DateTimeInterface $time): void
    {
        $this->now = \DateTimeImmutable::createFromInterface($time);
        $this->tickingFrom = new \DateTimeImmutable('now');
    }

    public function today(): Date
    {
        return Date::fromDateTime($this->now());
    }

    public function tickingFrom(): \DateTimeImmutable
    {
        return $this->tickingFrom;
    }

    private function calculateOffset(): \DateInterval
    {
        return $this->tickingFrom->diff(new \DateTimeImmutable('now'));
    }
}
