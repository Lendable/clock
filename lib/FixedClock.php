<?php

declare(strict_types=1);

namespace Lendable\Clock;

/**
 * Simple in-memory fixed clock.
 */
final class FixedClock implements Clock
{
    private const ISO8601_MICROSECONDS_FORMAT = 'Y-m-d\TH:i:s.uP';

    /**
     * @var \DateTimeImmutable
     */
    private $now;

    public function __construct(\DateTimeImmutable $now)
    {
        $this->now = $now;
    }

    public function now(): \DateTimeImmutable
    {
        return $this->now;
    }

    public function nowMutable(): \DateTime
    {
        $nowImmutable = $this->now();

        $nowMutable = \PHP_VERSION_ID >= 70300
            ? \DateTime::createFromImmutable($nowImmutable)
            : \DateTime::createFromFormat(self::ISO8601_MICROSECONDS_FORMAT, $nowImmutable->format(self::ISO8601_MICROSECONDS_FORMAT));

        \assert($nowMutable instanceof \DateTime);

        return $nowMutable;
    }
}
