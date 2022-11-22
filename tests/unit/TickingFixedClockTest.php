<?php

declare(strict_types=1);

namespace Tests\Lendable\Clock\Unit;

use Lendable\Clock\TickingFixedClock;
use PHPUnit\Framework\TestCase;
use Tests\Lendable\Clock\Support\TickingTimeAssertions;

final class TickingFixedClockTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_time_that_has_ticked(): void
    {
        $timeString = '2018-04-07T16:51:29.083869';
        $timeFormat = 'Y-m-d\TH:i:s.u';
        $now = \DateTimeImmutable::createFromFormat($timeFormat, $timeString, new \DateTimeZone('UTC'));
        \assert($now instanceof \DateTimeImmutable);
        $clock = TickingFixedClock::tickingFromCurrentTime($now);

        TickingTimeAssertions::assertDateTimeLessThanOneSecondAfter(
            $clock->tickingFrom(),
            new \DateTimeImmutable('now'),
        );

        // Immutable variant.

        $sample1 = $clock->now();
        $sample2 = $clock->now();
        $sample3 = $clock->now();

        $this->assertNotSame($sample1->format($timeFormat), $sample2->format($timeFormat));
        $this->assertNotSame($sample1->format($timeFormat), $sample3->format($timeFormat));
        $this->assertNotSame($sample2->format($timeFormat), $sample3->format($timeFormat));

        $this->assertGreaterThan($sample1, $sample2);
        $this->assertGreaterThan($sample1, $sample3);
        $this->assertGreaterThan($sample2, $sample3);

        TickingTimeAssertions::assertDateTimeLessThanOneSecondAfter($now, $sample1);
        TickingTimeAssertions::assertDateTimeLessThanOneSecondAfter($now, $sample2);
        TickingTimeAssertions::assertDateTimeLessThanOneSecondAfter($now, $sample3);

        // Mutable variant.

        $sample1 = $clock->nowMutable();
        $sample2 = $clock->nowMutable();
        $sample3 = $clock->nowMutable();

        $this->assertNotSame($sample1->format($timeFormat), $sample2->format($timeFormat));
        $this->assertNotSame($sample1->format($timeFormat), $sample3->format($timeFormat));
        $this->assertNotSame($sample2->format($timeFormat), $sample3->format($timeFormat));

        $this->assertGreaterThan($sample1, $sample2);
        $this->assertGreaterThan($sample1, $sample3);
        $this->assertGreaterThan($sample2, $sample3);

        TickingTimeAssertions::assertDateTimeLessThanOneSecondAfter($now, $sample1);
        TickingTimeAssertions::assertDateTimeLessThanOneSecondAfter($now, $sample2);
        TickingTimeAssertions::assertDateTimeLessThanOneSecondAfter($now, $sample3);
    }

    /**
     * @test
     */
     public function it_changes_the_time(): void
     {
         $timeString = '2021-05-05T14:11:49.128311';
         $timeFormat = 'Y-m-d\TH:i:s.u';
         $now = \DateTimeImmutable::createFromFormat($timeFormat, $timeString, new \DateTimeZone('UTC'));
         \assert($now instanceof \DateTimeImmutable);
         $clock = TickingFixedClock::tickingFromCurrentTime($now);

         $updatedTime = $now->modify('+30 minutes');

         $clock->changeTimeTo($updatedTime);

         TickingTimeAssertions::assertDateTimeLessThanOneSecondAfter($updatedTime, $clock->now());
         TickingTimeAssertions::assertDateTimeLessThanOneSecondAfter($updatedTime, $clock->nowMutable());
     }

    /**
     * @test
     */
    public function it_returns_a_date_object(): void
    {
        $timeString = '2018-04-07T16:51:29.083869';
        $timeFormat = 'Y-m-d\TH:i:s.u';
        $now = \DateTimeImmutable::createFromFormat($timeFormat, $timeString, new \DateTimeZone('UTC'));
        \assert($now instanceof \DateTimeImmutable);
        $clock = TickingFixedClock::tickingFromCurrentTime($now);

        $date = $clock->today();

        $this->assertSame(2018, $date->year());
        $this->assertSame(4, $date->month());
        $this->assertSame(7, $date->day());
    }

    /**
     * @test
     */
    public function it_returns_a_date_object_of_the_following_day_if_fixed_far_enough_back_in_the_past(): void
    {
        $timeString = '2018-04-07T16:51:29.083869';
        $timeFormat = 'Y-m-d\TH:i:s.u';
        $now = \DateTimeImmutable::createFromFormat($timeFormat, $timeString, new \DateTimeZone('UTC'));
        \assert($now instanceof \DateTimeImmutable);
        $clock = TickingFixedClock::tickingFromTime($now, (new \DateTimeImmutable('now'))->sub(new \DateInterval('PT24H')));

        $date = $clock->today();

        $this->assertSame(2018, $date->year());
        $this->assertSame(4, $date->month());
        $this->assertSame(8, $date->day());
    }
}
