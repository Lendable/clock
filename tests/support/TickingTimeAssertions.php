<?php

declare(strict_types=1);

namespace Tests\Lendable\Clock\Support;

use PHPUnit\Framework\Assert;

final class TickingTimeAssertions
{
    private function __construct()
    {
    }

    public static function assertDateTimeLessThanOneSecondAfter(
        \DateTimeInterface $expected,
        \DateTimeInterface $actual
    ): void {
        Assert::assertLessThan(1, $actual->format('U.u') - $expected->format('U.u'));
    }
}
