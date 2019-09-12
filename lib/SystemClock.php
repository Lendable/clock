<?php

declare(strict_types=1);

namespace Lendable\Clock;

/**
 * Delegates through to PHP to obtain the current system time in a fixed timezone.
 */
final class SystemClock implements Clock
{
    private const DEFAULT_TIMEZONE = 'UTC';

    private const ISO8601_MICROSECONDS_FORMAT = 'Y-m-d\TH:i:s.uP';

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
        // @TODO Use \DateTime::createFromImmutable when PHP version is >=7.3
        $instance = \DateTime::createFromFormat(self::ISO8601_MICROSECONDS_FORMAT, $this->now()->format(self::ISO8601_MICROSECONDS_FORMAT));
        \assert($instance instanceof \DateTime);

        return $instance;
    }
}
