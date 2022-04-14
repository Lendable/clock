<?php

declare(strict_types=1);

namespace Lendable\Clock;

use Lendable\Clock\Date\InvalidDate;

final class Date
{
    private int $year;

    private int $month;

    private int $day;

    private function __construct(int $year, int $month, int $day)
    {
        if (!\checkdate($month, $day, $year)) {
            throw InvalidDate::fromDate($year, $month, $day);
        }

        $this->year = $year;
        $this->month = $month;
        $this->day = $day;
    }

    public static function fromYearMonthDay(int $year, int $month, int $day): self
    {
        return new self($year, $month, $day);
    }

    public static function fromDateTime(\DateTimeInterface $dateTime): self
    {
        return new self(
            (int) $dateTime->format('Y'),
            (int) $dateTime->format('m'),
            (int) $dateTime->format('d')
        );
    }

    public static function fromYearMonthDayString(string $value): self
    {
        $result = \preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $value, $matches);

        if ($result === 0) {
            throw new InvalidDate('Failed to parse string as a Y-m-d formatted date.');
        }

        return self::fromYearMonthDay(
            (int) $matches[1],
            (int) $matches[2],
            (int) $matches[3]
        );
    }

    public static function fromYearsAgo(int $yearsAgo): self
    {
        $dob = new \DateTimeImmutable("today - $yearsAgo years");

        return self::fromDateTime($dob);
    }

    public function year(): int
    {
        return $this->year;
    }

    public function month(): int
    {
        return $this->month;
    }

    public function day(): int
    {
        return $this->day;
    }

    public function equals(self $other): bool
    {
        return $this->year === $other->year
            && $this->month === $other->month
            && $this->day === $other->day;
    }

    public function isBefore(self $other): bool
    {
        return $this->toDateTime() < $other->toDateTime();
    }

    public function isBeforeOrEqualTo(self $other): bool
    {
        return $this->isBefore($other) || $this->equals($other);
    }

    public function isAfter(self $other): bool
    {
        return $this->toDateTime() > $other->toDateTime();
    }

    public function isBetween(self $start, self $end): bool
    {
        return !$this->isBefore($start) && !$this->isAfter($end);
    }

    public function offsetByDays(int $days): self
    {
        if ($days === 0) {
            return $this;
        }

        return $this->modify(\sprintf('%d days', $days));
    }

    public function toDateTime(): \DateTimeImmutable
    {
        return DateTimeFactory::immutableFromFormat(
            'Y-m-d H:i:s',
            \sprintf('%d-%d-%d 00:00:00', $this->year, $this->month, $this->day),
        );
    }

    public function toYearMonthDayString(): string
    {
        return \sprintf('%d-%02d-%02d', $this->year, $this->month, $this->day);
    }

    public function diff(self $other): \DateInterval
    {
        return $this->toDateTime()->diff($other->toDateTime());
    }

    public function differenceInDays(self $other): int
    {
        return (int) $this->diff($other)->format('%a');
    }

    public function dayAfter(): self
    {
        return $this->modify('+1 days');
    }

    public function dayBefore(): self
    {
        return $this->modify('-1 days');
    }

    private function modify(string $modification): self
    {
        return self::fromDateTime($this->toDateTime()->modify($modification));
    }
}
