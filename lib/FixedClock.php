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
        // @TODO Use \DateTime::createFromImmutable when PHP version is >=7.3
        $instance = \DateTime::createFromFormat(self::ISO8601_MICROSECONDS_FORMAT, $this->now->format(self::ISO8601_MICROSECONDS_FORMAT));
        \assert($instance instanceof \DateTime);

        return $instance;
    }
}
