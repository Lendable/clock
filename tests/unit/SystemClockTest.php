<?php

declare(strict_types=1);

namespace Tests\Lendable\Clock\Unit;

use Lendable\Clock\SystemClock;
use PHPUnit\Framework\TestCase;

final class SystemClockTest extends TestCase
{
    public function exampleTimeZoneNames(): iterable
    {
        yield ['UTC'];
        yield ['America/New_York'];
        yield ['Europe/London'];
        yield ['Europe/Paris'];
    }

    /**
     * @test
     * @dataProvider exampleTimeZoneNames
     */
    public function it_gives_a_time_in_the_configured_timezone(string $timeZoneName): void
    {
        $clock = new SystemClock(new \DateTimeZone($timeZoneName));

        $this->assertSame($timeZoneName, $clock->now()->getTimezone()->getName());
    }

    /**
     * @test
     */
    public function it_gives_the_current_system_time(): void
    {
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $clock = new SystemClock(new \DateTimeZone('UTC'));

        $difference = $clock->now()->getTimestamp() - $now->getTimestamp();

        $this->assertGreaterThanOrEqual(0, $difference);
        $this->assertLessThanOrEqual(1, $difference);
    }
}
