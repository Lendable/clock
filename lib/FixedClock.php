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
        $now = $this->now();

        return method_exists('\DateTime', 'createFromImmutable')
            ? \DateTime::createFromImmutable($now)
            : \DateTime::createFromFormat(self::ISO8601_MICROSECONDS_FORMAT, $now->format(self::ISO8601_MICROSECONDS_FORMAT));
    }
}
