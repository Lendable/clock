<?php

declare(strict_types=1);

namespace Tests\Lendable\Clock\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DisableReturnValueGenerationForTestDoubles;
use PHPUnit\Framework\Attributes\Test;
use Lendable\Clock\TickingMockClock;
use PHPUnit\Framework\TestCase;
use Tests\Lendable\Clock\Support\TickingTimeAssertions;

#[DisableReturnValueGenerationForTestDoubles]
#[CoversClass(TickingMockClock::class)]
final class TickingMockClockTest extends TestCase
{
    #[Test]
    public function it_returns_time_that_has_ticked(): void
    {
        $timeString = '2018-04-07T16:51:29.083869';
        $timeFormat = 'Y-m-d\TH:i:s.u';
        $now = \DateTimeImmutable::createFromFormat($timeFormat, $timeString, new \DateTimeZone('UTC'));
        \assert($now instanceof \DateTimeImmutable);
        $clock = TickingMockClock::tickingFromCurrentTime($now);

        TickingTimeAssertions::assertDateTimeLessThanOneSecondAfter(
            $clock->tickingFrom(),
            new \DateTimeImmutable('now'),
        );

        // Immutable variant.

        $sample1 = $clock->now();
        \usleep(1);
        $sample2 = $clock->now();
        \usleep(1);
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
        \usleep(1);
        $sample2 = $clock->nowMutable();
        \usleep(1);
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

    #[Test]
    public function it_changes_the_time(): void
    {
        $timeString = '2021-05-05T14:11:49.128311';
        $timeFormat = 'Y-m-d\TH:i:s.u';
        $now = \DateTimeImmutable::createFromFormat($timeFormat, $timeString, new \DateTimeZone('UTC'));
        \assert($now instanceof \DateTimeImmutable);
        $clock = TickingMockClock::tickingFromCurrentTime($now);

        $updatedTime = $now->modify('+30 minutes');

        $clock->changeTimeTo($updatedTime);

        TickingTimeAssertions::assertDateTimeLessThanOneSecondAfter($updatedTime, $clock->now());
        TickingTimeAssertions::assertDateTimeLessThanOneSecondAfter($updatedTime, $clock->nowMutable());
    }

    #[Test]
    public function it_provides_the_date_from_its_current_time(): void
    {
        $timeString = '2018-04-07T16:51:29.083869';
        $timeFormat = 'Y-m-d\TH:i:s.u';
        $now = \DateTimeImmutable::createFromFormat($timeFormat, $timeString, new \DateTimeZone('UTC'));
        \assert($now instanceof \DateTimeImmutable);
        $clock = TickingMockClock::tickingFromCurrentTime($now);

        $date = $clock->today();

        $this->assertSame(2018, $date->year());
        $this->assertSame(4, $date->month());
        $this->assertSame(7, $date->day());
    }

    #[Test]
    public function it_advances_the_date_once_it_has_been_ticking_for_24_hours(): void
    {
        $timeString = '2018-04-07T16:51:29.083869';
        $timeFormat = 'Y-m-d\TH:i:s.u';
        $now = \DateTimeImmutable::createFromFormat($timeFormat, $timeString, new \DateTimeZone('UTC'));
        \assert($now instanceof \DateTimeImmutable);
        $clock = TickingMockClock::tickingFromTime($now, (new \DateTimeImmutable('now'))->sub(new \DateInterval('PT24H')));

        $date = $clock->today();

        $this->assertSame(2018, $date->year());
        $this->assertSame(4, $date->month());
        $this->assertSame(8, $date->day());
    }

    #[Test]
    public function it_can_rewind_time(): void
    {
        $timeString = '2021-05-05T14:11:49.128311';
        $timeFormat = 'Y-m-d\TH:i:s.u';
        $now = \DateTimeImmutable::createFromFormat($timeFormat, $timeString, new \DateTimeZone('UTC'));
        \assert($now instanceof \DateTimeImmutable);
        $clock = TickingMockClock::tickingFromCurrentTime($now);

        $clock->rewindTimeBy(new \DateInterval('PT30M'));

        TickingTimeAssertions::assertDateTimeLessThanOneSecondAfter($now->sub(new \DateInterval('PT30M')), $clock->now());
        TickingTimeAssertions::assertDateTimeLessThanOneSecondAfter($now->sub(new \DateInterval('PT30M')), $clock->nowMutable());
    }

    #[Test]
    public function it_can_advance_time(): void
    {
        $timeString = '2021-05-05T14:11:49.128311';
        $timeFormat = 'Y-m-d\TH:i:s.u';
        $now = \DateTimeImmutable::createFromFormat($timeFormat, $timeString, new \DateTimeZone('UTC'));
        \assert($now instanceof \DateTimeImmutable);
        $clock = TickingMockClock::tickingFromCurrentTime($now);

        $clock->advanceTimeBy(new \DateInterval('PT30M'));

        TickingTimeAssertions::assertDateTimeLessThanOneSecondAfter($now->add(new \DateInterval('PT30M')), $clock->now());
        TickingTimeAssertions::assertDateTimeLessThanOneSecondAfter($now->add(new \DateInterval('PT30M')), $clock->nowMutable());
    }
}
