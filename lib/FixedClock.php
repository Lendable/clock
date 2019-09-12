<?php

declare(strict_types=1);

namespace Lendable\Clock;

/**
 * Simple in-memory fixed clock.
 */
final class FixedClock implements Clock
{
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
}
