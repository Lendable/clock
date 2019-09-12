<?php

declare(strict_types=1);

namespace Lendable\Clock;

/**
 * Simple in-memory fixed clock.
 */
final class FixedClock implements Clock
{
    private const COMPLETE_DATETIME_FORMAT = 'Y-m-d\TH:i:s.uP';

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
        return \DateTime::createFromFormat(self::COMPLETE_DATETIME_FORMAT, $this->now->format(self::COMPLETE_DATETIME_FORMAT));
    }
}
