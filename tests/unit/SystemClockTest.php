<?php

declare(strict_types=1);

namespace Tests\Lendable\Clock\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Lendable\Clock\SystemClock;
use PHPUnit\Framework\TestCase;

final class SystemClockTest extends TestCase
{
    /**
     * @phpstan-return iterable<array{0: string}>
     */
    public static function exampleTimeZoneNames(): iterable
    {
        yield ['UTC'];
        yield ['America/New_York'];
        yield ['Europe/London'];
        yield ['Europe/Paris'];
    }

    #[DataProvider('exampleTimeZoneNames')]
    #[Test]
    public function it_gives_a_time_in_the_configured_timezone(string $timeZoneName): void
    {
        $clock = new SystemClock(new \DateTimeZone($timeZoneName));

        $this->assertSame($timeZoneName, $clock->now()->getTimezone()->getName());
    }

    #[Test]
    public function it_defaults_timezone_to_utc(): void
    {
        $this->assertSame('UTC', (new SystemClock())->now()->getTimezone()->getName());
    }

    #[Test]
    public function it_gives_the_current_system_time(): void
    {
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $clock = new SystemClock(new \DateTimeZone('UTC'));

        $difference = $clock->now()->getTimestamp() - $now->getTimestamp();

        $this->assertGreaterThanOrEqual(0, $difference);
        $this->assertLessThanOrEqual(1, $difference);

        $difference = $clock->nowMutable()->getTimestamp() - $now->getTimestamp();

        $this->assertGreaterThanOrEqual(0, $difference);
        $this->assertLessThanOrEqual(1, $difference);
    }

    #[Test]
    public function it_can_return_a_date_object(): void
    {
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

        $currentYear = $now->format('Y');
        $currentMonth = $now->format('m');
        $currentDay = $now->format('d');

        $systemClock = new SystemClock(new \DateTimeZone('UTC'));

        $date = $systemClock->today();

        $this->assertEquals($currentYear, $date->year());
        $this->assertEquals($currentMonth, $date->month());
        $this->assertEquals($currentDay, $date->day());
    }
}
