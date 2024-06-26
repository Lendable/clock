<?php

declare(strict_types=1);

namespace Tests\Lendable\Clock\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DisableReturnValueGenerationForTestDoubles;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Lendable\Clock\PersistedFixedClock;
use Lendable\Clock\Serialization\FixedFileNameGenerator;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

#[DisableReturnValueGenerationForTestDoubles]
#[CoversClass(PersistedFixedClock::class)]
final class PersistedFixedClockTest extends TestCase
{
    #[Test]
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

    #[Test]
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

    #[Test]
    public function it_can_return_a_date_object(): void
    {
        $vfs = vfsStream::setup('serialized_time');
        $timeString = '2018-04-07T16:51:29.083869';
        $timeFormat = 'Y-m-d\TH:i:s.u';
        $now = \DateTimeImmutable::createFromFormat($timeFormat, $timeString, new \DateTimeZone('UTC'));
        \assert($now instanceof \DateTimeImmutable);
        $clock = PersistedFixedClock::initializeWith($vfs->url(), new FixedFileNameGenerator(), $now);

        $date = $clock->today();

        $this->assertEquals(2018, $date->year());
        $this->assertEquals(4, $date->month());
        $this->assertEquals(7, $date->day());
    }

    #[Test]
    public function it_can_change_the_time_to_a_new_fixed_value(): void
    {
        $vfs = vfsStream::setup('serialized_time');
        $timeString = '2021-05-05T14:11:49.128311';
        $timeFormat = 'Y-m-d\TH:i:s.u';
        $now = \DateTimeImmutable::createFromFormat($timeFormat, $timeString, new \DateTimeZone('UTC'));
        \assert($now instanceof \DateTimeImmutable);
        $fileNameGenerator = new FixedFileNameGenerator();
        $clock = PersistedFixedClock::initializeWith($vfs->url(), $fileNameGenerator, $now);

        $updatedTime = $now->modify('+30 minutes');

        $clock->changeTimeTo($updatedTime);
        $expectedTimestamp = '2021-05-05T14:41:49.128311';

        $this->assertSame($expectedTimestamp, $clock->now()->format($timeFormat));
        $this->assertSame($expectedTimestamp, $clock->nowMutable()->format($timeFormat));

        // Verify that this value was persisted.
        $clock = PersistedFixedClock::fromPersisted($vfs->url(), $fileNameGenerator);

        $this->assertSame($expectedTimestamp, $clock->now()->format($timeFormat));
        $this->assertSame($expectedTimestamp, $clock->nowMutable()->format($timeFormat));
    }

    #[Test]
    public function it_can_rewind_time(): void
    {
        $vfs = vfsStream::setup('serialized_time');
        $timeString = '2021-05-05T14:11:49.128311';
        $timeFormat = 'Y-m-d\TH:i:s.u';
        $now = \DateTimeImmutable::createFromFormat($timeFormat, $timeString, new \DateTimeZone('UTC'));
        \assert($now instanceof \DateTimeImmutable);
        $fileNameGenerator = new FixedFileNameGenerator();
        $clock = PersistedFixedClock::initializeWith($vfs->url(), $fileNameGenerator, $now);

        $clock->rewindTimeBy(new \DateInterval('PT30M'));

        $expectedTimestamp = '2021-05-05T13:41:49.128311';

        $this->assertSame($expectedTimestamp, $clock->now()->format($timeFormat));
        $this->assertSame($expectedTimestamp, $clock->nowMutable()->format($timeFormat));

        // Verify that this value was persisted.
        $clock = PersistedFixedClock::fromPersisted($vfs->url(), $fileNameGenerator);

        $this->assertSame($expectedTimestamp, $clock->now()->format($timeFormat));
        $this->assertSame($expectedTimestamp, $clock->nowMutable()->format($timeFormat));
    }

    #[Test]
    public function it_can_advance_time(): void
    {
        $vfs = vfsStream::setup('serialized_time');
        $timeString = '2021-05-05T14:11:49.128311';
        $timeFormat = 'Y-m-d\TH:i:s.u';
        $now = \DateTimeImmutable::createFromFormat($timeFormat, $timeString, new \DateTimeZone('UTC'));
        \assert($now instanceof \DateTimeImmutable);
        $fileNameGenerator = new FixedFileNameGenerator();
        $clock = PersistedFixedClock::initializeWith($vfs->url(), $fileNameGenerator, $now);

        $clock->advanceTimeBy(new \DateInterval('PT30M'));

        $expectedTimestamp = '2021-05-05T14:41:49.128311';

        $this->assertSame($expectedTimestamp, $clock->now()->format($timeFormat));
        $this->assertSame($expectedTimestamp, $clock->nowMutable()->format($timeFormat));

        // Verify that this value was persisted.
        $clock = PersistedFixedClock::fromPersisted($vfs->url(), $fileNameGenerator);

        $this->assertSame($expectedTimestamp, $clock->now()->format($timeFormat));
        $this->assertSame($expectedTimestamp, $clock->nowMutable()->format($timeFormat));
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function provideInvalidData(): iterable
    {
        yield 'int' => ['1', 'Expected data to decode to an array, but got int.'];
        yield 'bool' => ['true', 'Expected data to decode to an array, but got bool.'];
        yield 'string' => ['"foo"', 'Expected data to decode to an array, but got string.'];
        yield 'float' => ['10.0', 'Expected data to decode to an array, but got float.'];
        yield 'empty array' => [
            '[]',
            'Expected to decode to an associative array containing keys timestamp and timezone. Got keys [].',
        ];
        yield 'empty object' => [
            '{}',
            'Expected to decode to an associative array containing keys timestamp and timezone. Got keys [].',
        ];
        yield 'missing both keys' => [
            '{"foo": "bar"}',
            'Expected to decode to an associative array containing keys timestamp and timezone. Got keys ["foo"].',
        ];
        yield 'missing timestamp' => [
            '{"bar": "baz", "timezone": "UTC"}',
            'Expected to decode to an associative array containing keys timestamp and timezone. Got keys ["bar", "timezone"].',
        ];
        yield 'missing timezone' => [
            '{"bar": "baz", "timestamp": "2021-05-05T14:11:49.128311"}',
            'Expected to decode to an associative array containing keys timestamp and timezone. Got keys ["bar", "timestamp"].',
        ];
        yield 'non-string timestamp' => [
            '{"timezone": "UTC", "timestamp": 12345678}',
            'Expected key "timestamp" to be a string, but it was a int.',
        ];
        yield 'non-string timezone' => [
            '{"timezone": -3, "timestamp": "2021-05-05T14:11:49.128311"}',
            'Expected key "timezone" to be a string, but it was a int.',
        ];
    }

    #[DataProvider('provideInvalidData')]
    #[Test]
    public function it_throws_when_loaded_data_is_not_an_array_or_is_invalid(string $serializedData, string $expectedExceptionMessage): void
    {
        $vfs = vfsStream::setup('serialized_time');
        $fileNameGenerator = new FixedFileNameGenerator();
        \file_put_contents($vfs->url().'/'.$fileNameGenerator->generate(), $serializedData);

        try {
            PersistedFixedClock::fromPersisted($vfs->url(), $fileNameGenerator);

            $this->fail('Expected an exception to be thrown, but one was not.');
        } catch (\RuntimeException $exception) {
            $this->assertSame($expectedExceptionMessage, $exception->getMessage());
        }
    }
}
