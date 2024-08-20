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

    public function isAfterOrEqualTo(self $other): bool
    {
        return $this->isAfter($other) || $this->equals($other);
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
     * Provides a date time representation of this date in UTC.
     */
    public function toDateTime(): \DateTimeImmutable
    {
        return DateTimeFactory::immutableFromFormat(
            'Y-m-d H:i:s',
            \sprintf('%d-%d-%d 00:00:00', $this->year, $this->month, $this->day),
            new \DateTimeZone('UTC'),
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
