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

        $this->assertSame($timeString, PersistedFixedClock::initializeWith($vfs->url(), $fileNameGenerator, $now)->now()->format($timeFormat));
        $this->assertSame($timeString, PersistedFixedClock::fromPersisted($vfs->url(), $fileNameGenerator)->now()->format($timeFormat));
        $this->assertSame($timeString, PersistedFixedClock::fromPersisted($vfs->url(), $fileNameGenerator)->now()->format($timeFormat));
    }
}
