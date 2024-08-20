<?php

declare(strict_types=1);

namespace Lendable\Clock;

use Lendable\Clock\Date\InvalidDate;

final readonly class Date
{
    /**
     * @throws InvalidDate
     */
    private function __construct(
        private int $year,
        private int $month,
        private int $day,
    ) {
        if (!\checkdate($month, $day, $year)) {
            throw InvalidDate::fromDate($year, $month, $day);
        }
    }

    /**
     * @throws InvalidDate
     */
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

    /**
     * @throws InvalidDate
     */
    public static function fromYearMonthDayString(string $value): self
    {
        $result = \preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $value, $matches);

        if ($result === 0) {
            throw new InvalidDate('Failed to parse string as a Y-m-d formatted date.');
        }

        /** @var list{non-empty-string, numeric-string, numeric-string, numeric-string} $matches */
        [, $year, $month, $day] = $matches;

        return self::fromYearMonthDay((int) $year, (int) $month, (int) $day);
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
        return $this->startOfDay() < $other->startOfDay();
    }

    public function isBeforeOrEqualTo(self $other): bool
    {
        return $this->isBefore($other) || $this->equals($other);
    }

    public function isAfter(self $other): bool
    {
        return $this->startOfDay() > $other->startOfDay();
    }

    public function isAfterOrEqualTo(self $other): bool
    {
        return $this->isAfter($other) || $this->equals($other);
    }

    public function isBetween(self $start, self $end): bool
    {
        return !$this->isBefore($start) && !$this->isAfter($end);
    }

    /**
     * Returns a new instance of Date with 1 month added to current.
     * If next month has fewer days than current day, the day will be capped at that value.
     */
    public function addMonth(): self
    {
        $day = 1;
        $month = $this->month + 1;
        $year = $this->year;
        if ($month > 12) {
            $month = 1;
            $year++;
        }

        return new self(
            $year,
            $month,
            \min(
                $this->day,
                (int) DateTimeFactory::immutableFromFormat(
                    'Y-m-d',
                    \sprintf('%02d-%02d-%02d', $year, $month, $day),
                )->format('t'),
            ),
        );
    }

    public function offsetByDays(int $days): self
    {
        if ($days === 0) {
            return $this;
        }

        return $this->modify(\sprintf('%d days', $days));
    }

    public function offsetByMonths(int $months): self
    {
        if ($months === 0) {
            return $this;
        }

        return $this->modify(\sprintf('%d months', $months));
    }

    public function offsetByYears(int $years): self
    {
        if ($years === 0) {
            return $this;
        }

        return $this->modify(\sprintf('%d years', $years));
    }

    /**
     * Returns an instance of \DateTimeImmutable shifted to start of day in a given timezone (falls back to system default)
     */
    public function startOfDay(\DateTimeZone $timezone = null): \DateTimeImmutable
    {
        return DateTimeFactory::immutableFromFormat('Y-m-d', $this->toYearMonthDayString(), $timezone)->setTime(0, 0);
    }

    /**
     * Returns an instance of \DateTimeImmutable shifted to end of day in a given timezone (falls back to system default)
     */
    public function endOfDay(\DateTimeZone $timezone = null): \DateTimeImmutable
    {
        return DateTimeFactory::immutableFromFormat('Y-m-d', $this->toYearMonthDayString(), $timezone)
            ->setTime(23, 59, 59, 999999);
    }

    public function toYearMonthDayString(): string
    {
        return \sprintf('%d-%02d-%02d', $this->year, $this->month, $this->day);
    }

    public function diff(self $other): \DateInterval
    {
        return $this->startOfDay()->diff($other->startOfDay());
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
        return self::fromDateTime($this->startOfDay()->modify($modification));
    }
}
