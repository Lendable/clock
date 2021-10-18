<?php

declare(strict_types=1);

namespace Lendable\Clock\Date;

final class InvalidDate extends \InvalidArgumentException
{
    public static function fromDate(int $year, int $month, int $day): self
    {
        return new self(\sprintf('Date %d-%d-%d (Y-m-d) is invalid.', $year, $month, $day));
    }
}
