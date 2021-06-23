<?php

declare(strict_types=1);

namespace Tests\Lendable\Clock\Unit;

use Lendable\Clock\DateTimeNormalizer;
use PHPUnit\Framework\TestCase;

final class DateTimeNormalizerTest extends TestCase
{
    private const FORMAT = 'Y-m-d\TH:i:s.u';

    /**
     * @phpstan-return iterable<array{\DateTimeInterface, string, string}>
     */
    public function provideNormalisationCases(): iterable
    {
        $defaultTimestamp = '2021-05-05T14:11:49.128311';
        $defaultTimeZone = new \DateTimeZone('UTC');

        $immutable = \DateTimeImmutable::createFromFormat(self::FORMAT, $defaultTimestamp, $defaultTimeZone);
        \assert($immutable instanceof \DateTimeImmutable);

        yield [$immutable, 'UTC', $defaultTimestamp];

        $mutable = \DateTime::createFromFormat(self::FORMAT, $defaultTimestamp, $defaultTimeZone);
        \assert($mutable instanceof \DateTime);

        yield [$mutable, 'UTC', $defaultTimestamp];
    }

    /**
     * @test
     * @dataProvider provideNormalisationCases
     */
    public function it_can_create_normalise_instances(
        \DateTimeInterface $time,
        string $expectedTimeZone,
        string $expectedFormattedValue
    ): void {
        $normalized = DateTimeNormalizer::immutable($time);

        $this->assertSame($expectedTimeZone, $normalized->getTimezone()->getName());
        $this->assertSame($expectedFormattedValue, $normalized->format(self::FORMAT));

        $normalized = DateTimeNormalizer::mutable($time);

        $this->assertSame($expectedTimeZone, $normalized->getTimezone()->getName());
        $this->assertSame($expectedFormattedValue, $normalized->format(self::FORMAT));
    }

    /**
     * @test
     */
    public function it_does_not_return_the_same_instance_when_given_mutable_and_asked_for_mutable(): void
    {
        $now = new \DateTime();
        $normalized = DateTimeNormalizer::mutable($now);

        $this->assertNotSame($now, $normalized);
    }
}
