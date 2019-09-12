<?php

declare(strict_types=1);

namespace Tests\Lendable\Clock\Unit;

use Lendable\Clock\FixedClock;
use PHPUnit\Framework\TestCase;

final class FixedClockTest extends TestCase
{
    /**
     * @test
     */
    public function it_always_returns_the_given_time(): void
    {
        $timeString = '2018-04-07T16:51:29.083869';
        $timeFormat = 'Y-m-d\TH:i:s.u';
        $now = \DateTimeImmutable::createFromFormat($timeFormat, $timeString, new \DateTimeZone('UTC'));
        \assert($now instanceof \DateTimeImmutable);
        $clock = new FixedClock($now);

        $this->assertSame($timeString, $clock->now()->format($timeFormat));
        $this->assertSame($timeString, $clock->now()->format($timeFormat));
        $this->assertSame($timeString, $clock->now()->format($timeFormat));
    }
}
