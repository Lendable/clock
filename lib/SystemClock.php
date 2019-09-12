<?php

declare(strict_types=1);

namespace Lendable\Clock;

/**
 * Delegates through to PHP to obtain the current system time in a fixed timezone.
 */
final class SystemClock implements Clock
{
    private const DEFAULT_TIMEZONE = 'UTC';
    private const COMPLETE_DATETIME_FORMAT = 'Y-m-d\TH:i:s.uP';

    /**
     * @var \DateTimeZone
     */
    private $timeZone;

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
        return \DateTime::createFromFormat(self::COMPLETE_DATETIME_FORMAT, $this->now()->format(self::COMPLETE_DATETIME_FORMAT));
    }
}
