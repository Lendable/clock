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

        $this->assertSame($timeString, $clock->nowMutable()->format($timeFormat));
        $this->assertSame($timeString, $clock->nowMutable()->format($timeFormat));
        $this->assertSame($timeString, $clock->nowMutable()->format($timeFormat));
    }

    /**
     * @test
     */
    public function it_can_change_the_time_to_a_new_fixed_value(): void
    {
        $timeString = '2021-05-05T14:11:49.128311';
        $timeFormat = 'Y-m-d\TH:i:s.u';
        $now = \DateTimeImmutable::createFromFormat($timeFormat, $timeString, new \DateTimeZone('UTC'));
        \assert($now instanceof \DateTimeImmutable);
        $clock = new FixedClock($now);

        $updatedTime = $now->modify('+30 minutes');

        $clock->changeTimeTo($updatedTime);

        $this->assertSame('2021-05-05T14:41:49.128311', $clock->now()->format($timeFormat));
        $this->assertSame('2021-05-05T14:41:49.128311', $clock->nowMutable()->format($timeFormat));
    }

    /**
     * @test
     */
    public function it_can_return_a_date_object(): void
    {
        $timeString = '2018-04-07T16:51:29.083869';
        $timeFormat = 'Y-m-d\TH:i:s.u';
        $now = \DateTimeImmutable::createFromFormat($timeFormat, $timeString, new \DateTimeZone('UTC'));
        \assert($now instanceof \DateTimeImmutable);
        $clock = new FixedClock($now);

        $date = $clock->today();

        $this->assertEquals(2018, $date->year());
        $this->assertEquals(4, $date->month());
        $this->assertEquals(7, $date->day());
    }
}
