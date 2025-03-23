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
     * @return self Instance incremented by the specified number of months.
     *              If the resulting month has fewer days than the current day, the day will be the last day of that month.
     *
     * @throws \InvalidArgumentException if $increment is less than 1
     */
    public function addMonths(int $increment): self
    {
        if ($increment < 1) {
            throw new \InvalidArgumentException('Months increment must be greater than 0.');
        }

        $month = $this->month + $increment;
        $year = $this->year;
        while ($month > 12) {
            $month -= 12;
            $year++;
        }

        return new self($year, $month, \min($this->day, $this->numberOfDaysInMonth($year, $month)));
    }

    /**
     * @return self Instance decremented by the specified number of months.
     *              If the resulting month has fewer days than the current day, the day will be the last day of that month.
     *
     * @throws \InvalidArgumentException if $decrement is less than 1
     */
    public function subMonths(int $decrement): self
    {
        if ($decrement < 1) {
            throw new \InvalidArgumentException('Months decrement must be greater than 0.');
        }

        $month = $this->month - $decrement;
        $year = $this->year;
        while ($month < 1) {
            $month += 12;
            $year--;
        }

        return new self($year, $month, \min($this->day, $this->numberOfDaysInMonth($year, $month)));
    }

    /**
     * @return self Instance with day part offset by a number of days, or $this if number is 0.
     *              The resulting Date can overflow to another month.
     */
    public function offsetByDays(int $days): self
    {
        if ($days === 0) {
            return $this;
        }

        return $this->modify(\sprintf('%d days', $days));
    }

    /**
     * @return self Instance with month part offset by a number of months, or $this if number is 0.
     *              The resulting Date can overflow to another month, modifying day part.
     */
    public function offsetByMonths(int $months): self
    {
        if ($months === 0) {
            return $this;
        }

        return $this->modify(\sprintf('%d months', $months));
    }

    /**
     * @return self Instance with year part offset by a number of years, or $this if number is 0.
     */
    public function offsetByYears(int $years): self
    {
        if ($years === 0) {
            return $this;
        }

        return $this->modify(\sprintf('%d years', $years));
    }

    /**
     * @return \DateTimeImmutable Instance shifted to start of day in a given timezone (falls back to system default)
     */
    public function startOfDay(\DateTimeZone $timezone = null): \DateTimeImmutable
    {
        return DateTimeFactory::immutableFromFormat('Y-m-d', $this->toYearMonthDayString(), $timezone)->setTime(0, 0);
    }

    /**
     * @return \DateTimeImmutable Instance shifted to end of day in a given timezone (falls back to system default)
     */
    public function endOfDay(\DateTimeZone $timezone = null): \DateTimeImmutable
    {
        return DateTimeFactory::immutableFromFormat('Y-m-d', $this->toYearMonthDayString(), $timezone)
            ->setTime(23, 59, 59, 999999);
    }

    /**
     * @return self Instance with day part shifted to the last day of current month.
     */
    public function endOfMonth(): self
    {
        $daysInMonth = $this->numberOfDaysInMonth($this->year, $this->month);
        if ($this->day === $daysInMonth) {
            return $this;
        }

        return new self($this->year, $this->month, $daysInMonth);
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

    /**
     * @return self Instance with modified day part, or $this if day remains unchanged
     *
     * @throws InvalidDate
     */
    public function withDay(int $day): self
    {
        if ($this->day === $day) {
            return $this;
        }

        return new self($this->year, $this->month, $day);
    }

    private function modify(string $modification): self
    {
        return self::fromDateTime($this->startOfDay()->modify($modification));
    }

    private function numberOfDaysInMonth(int $year, int $month): int
    {
        // @infection-ignore-all (IncrementInteger)
        return (int) DateTimeFactory::immutableFromFormat(
            'Y-m-d',
            \sprintf('%d-%02d-%02d', $year, $month, 1),
        )->format('t');
    }
}
