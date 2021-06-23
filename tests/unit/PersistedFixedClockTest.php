<?php

declare(strict_types=1);

namespace Tests\Lendable\Clock\Unit;

use Lendable\Clock\Serialization\FixedFileNameGenerator;
use Lendable\Clock\PersistedFixedClock;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

final class PersistedFixedClockTest extends TestCase
{
    /**
     * @test
     */
    public function it_reuses_the_serialized_value_when_reconstructed_from(): void
    {
        $vfs = vfsStream::setup('serialized_time');
        $fileNameGenerator = new FixedFileNameGenerator();
        $timeString = '2018-04-07T16:51:29.083869';
        $timeFormat = 'Y-m-d\TH:i:s.u';
        $now = \DateTimeImmutable::createFromFormat($timeFormat, $timeString, new \DateTimeZone('UTC'));
        \assert($now instanceof \DateTimeImmutable);

        $this->assertSame($timeString, PersistedFixedClock::initializeWith($vfs->url(), $fileNameGenerator, $now)->now()->format($timeFormat));
        $this->assertSame($timeString, PersistedFixedClock::fromPersisted($vfs->url(), $fileNameGenerator)->now()->format($timeFormat));
        $this->assertSame($timeString, PersistedFixedClock::fromPersisted($vfs->url(), $fileNameGenerator)->now()->format($timeFormat));
        $this->assertSame($timeString, PersistedFixedClock::fromPersisted($vfs->url(), $fileNameGenerator)->nowMutable()->format($timeFormat));
        $this->assertSame($timeString, PersistedFixedClock::fromPersisted($vfs->url(), $fileNameGenerator)->nowMutable()->format($timeFormat));
    }

    /**
     * @test
     */
    public function it_always_returns_the_given_time(): void
    {
        $vfs = vfsStream::setup('serialized_time');
        $timeString = '2018-04-07T16:51:29.083869';
        $timeFormat = 'Y-m-d\TH:i:s.u';
        $now = \DateTimeImmutable::createFromFormat($timeFormat, $timeString, new \DateTimeZone('UTC'));
        \assert($now instanceof \DateTimeImmutable);
        $clock = PersistedFixedClock::initializeWith($vfs->url(), new FixedFileNameGenerator(), $now);

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
        $vfs = vfsStream::setup('serialized_time');
        $timeString = '2021-05-05T14:11:49.128311';
        $timeFormat = 'Y-m-d\TH:i:s.u';
        $now = \DateTimeImmutable::createFromFormat($timeFormat, $timeString, new \DateTimeZone('UTC'));
        \assert($now instanceof \DateTimeImmutable);
        $clock = PersistedFixedClock::initializeWith($vfs->url(), new FixedFileNameGenerator(), $now);

        $updatedTime = $now->modify('+30 minutes');

        $clock->changeTimeTo($updatedTime);

        $this->assertSame('2021-05-05T14:41:49.128311', $clock->now()->format($timeFormat));
        $this->assertSame('2021-05-05T14:41:49.128311', $clock->nowMutable()->format($timeFormat));
    }
}
