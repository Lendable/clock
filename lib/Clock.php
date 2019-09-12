<?php

declare(strict_types=1);

namespace Lendable\Clock;

interface Clock
{
    public function now(): \DateTimeImmutable;

    public function nowMutable(): \DateTime;
}
