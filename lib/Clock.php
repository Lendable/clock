<?php

declare(strict_types=1);

namespace Lendable\Clock;

interface Clock
{
    public const MICROSECONDS_DATETIME_FORMAT = 'Y-m-d H:i:s.u';

    public function now(): \DateTimeImmutable;

    public function nowMutable(): \DateTime;
}
