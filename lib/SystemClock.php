<?php

declare(strict_types=1);

namespace Lendable\Clock;

/**
 * Delegates through to PHP to obtain the current system time in a fixed timezone.
 */
final class SystemClock implements Clock
{
    private const DEFAULT_TIMEZONE = 'UTC';

    private \DateTimeZone $timeZone;

    public function __construct(?\DateTimeZone $timeZone = null)
    {
        $this->timeZone = $timeZone ?? new \DateTimeZone(self::DEFAULT_TIMEZONE);
    }

    public function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable('now', $this->timeZone);
    }

    public function nowMutable(): \DateTime
    {
        return \DateTime::createFromImmutable($this->now());
    }

    public function today(): Date
    {
        return Date::fromDateTime($this->now());
    }
}
