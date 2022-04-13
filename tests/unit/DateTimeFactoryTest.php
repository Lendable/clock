<?php

declare(strict_types=1);

namespace Tests\Lendable\Clock\Unit;

use Lendable\Clock\DateTimeFactory;
use PHPUnit\Framework\TestCase;

final class DateTimeFactoryTest extends TestCase
{
    /**
     * @return iterable<array{string}>
     */
    public function provideTimezones(): iterable
    {
        yield ['Europe/Paris'];
        yield ['Europe/Berlin'];
        yield ['Europe/London'];
    }

    /**
     * @test
     * @dataProvider provideTimezones
     */
    public function it_creates_immutable_instances_with_the_system_default_timezone_if_one_is_not_provided(string $timezone): void
    {
        $this->runWithDefaultTimezone($timezone, function () use ($timezone): void {
            $timestamp = '2022-04-01 12:10:20';
            $format = 'Y-m-d H:i:s';

            $fixture = DateTimeFactory::immutableFromFormat($format, $timestamp);

            $this->assertTimestampAndTimezoneEquals($timestamp, $format, $timezone, $fixture);
        });
    }

    /**
     * @test
     */
    public function it_creates_immutable_instances_with_the_given_timezone(): void
    {
        $timestamp = '2022-04-03 10:20:30';
        $format = 'Y-m-d H:i:s';
        $timezone = 'America/New_York';
        $instance = DateTimeFactory::immutableFromFormat($format, $timestamp, new \DateTimeZone($timezone));

        $this->assertTimestampAndTimezoneEquals($timestamp, $format, $timezone, $instance);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_an_immutable_instance_fails_to_construct(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot create \DateTimeImmutable instance from format "ABC123" and value "1234567".');

        DateTimeFactory::immutableFromFormat('ABC123', '1234567');
    }

    /**
     * @test
     * @dataProvider provideTimezones
     */
    public function it_creates_mutable_instances_with_the_system_default_timezone_if_one_is_not_provided(string $timezone): void
    {
        $this->runWithDefaultTimezone($timezone, function () use ($timezone): void {
            $timestamp = '2022-04-01 12:10:20';
            $format = 'Y-m-d H:i:s';

            $fixture = DateTimeFactory::mutableFromFormat($format, $timestamp);

            $this->assertTimestampAndTimezoneEquals($timestamp, $format, $timezone, $fixture);
        });
    }

    /**
     * @test
     */
    public function it_creates_mutable_instances_with_the_given_timezone(): void
    {
        $timestamp = '2022-04-03 10:20:30';
        $format = 'Y-m-d H:i:s';
        $timezone = 'America/New_York';
        $instance = DateTimeFactory::mutableFromFormat($format, $timestamp, new \DateTimeZone($timezone));

        $this->assertTimestampAndTimezoneEquals($timestamp, $format, $timezone, $instance);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_a_mutable_instance_fails_to_construct(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot create \DateTime instance from format "ABC123" and value "1234567".');

        DateTimeFactory::mutableFromFormat('ABC123', '1234567');
    }

    /**
     * @param callable(): void $callback
     */
    private function runWithDefaultTimezone(string $timezone, callable $callback): void
    {
        $defaultTimezone = \date_default_timezone_get();

        try {
            \date_default_timezone_set($timezone);
            $callback();
        } finally {
            \date_default_timezone_set($defaultTimezone);
        }
    }

    private function assertTimestampAndTimezoneEquals(
        string $expectedTimestamp,
        string $timestampFormat,
        string $expectedTimezone,
        \DateTimeInterface $actual
    ): void {
        $this->assertSame($expectedTimestamp, $actual->format($timestampFormat));
        $this->assertSame($expectedTimezone, $actual->getTimezone()->getName());
    }
}
