<?php

declare(strict_types=1);

namespace Tests\Lendable\Clock\Support;

use PHPUnit\Framework\Assert;

final class TickingTimeAssertions
{
    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    public static function assertDateTimeLessThanOneSecondAfter(
        \DateTimeInterface $expected,
        \DateTimeInterface $actual
    ): void {
        Assert::assertNotSame($expected->format('Y-m-d\TH:i:s.u'), $actual->format('Y-m-d\TH:i:s.u'));
        Assert::assertGreaterThan($expected, $actual);
        Assert::assertLessThan(1, $actual->format('U.u') - $expected->format('U.u'));
    }
}
