<?php

declare(strict_types=1);

namespace Tests\Lendable\Clock\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\DisableReturnValueGenerationForTestDoubles;
use PHPUnit\Framework\Attributes\Test;
use Lendable\Clock\Date;
use Lendable\Clock\Date\InvalidDate;
use Lendable\Clock\DateTimeFactory;
use PHPUnit\Framework\TestCase;

#[DisableReturnValueGenerationForTestDoubles]
#[CoversClass(Date::class)]
final class DateTest extends TestCase
{
    /**
     * @return iterable<array{int, int, int, string}>
     */
    public static function provideValidIntegerComponentsAndExpectedStringRepresentationsData(): iterable
    {
        yield [
            2018,
            12,
            10,
            '2018-12-10',
        ];
        yield [
            2019,
            1,
            20,
            '2019-01-20',
        ];
        yield [
            2018,
            10,
            1,
            '2018-10-01',
        ];
    }

    #[Test]
    #[DataProvider('provideValidIntegerComponentsAndExpectedStringRepresentationsData')]
    public function it_can_be_constructed_from_integer_components(
        int $year,
        int $month,
        int $day,
        string $expectedStringRepresentation
    ): void {
        $result = Date::fromYearMonthDay($year, $month, $day);

        $this->assertSame($expectedStringRepresentation, $result->toYearMonthDayString());
    }

    #[Test]
    public function it_throws_when_constructing_from_integer_components_if_invalid_date(): void
    {
        $this->expectException(InvalidDate::class);
        $this->expectExceptionMessage('Date 2018-13-10 (Y-m-d) is invalid.');

        Date::fromYearMonthDay(2018, 13, 10);
    }

    /**
     * @return iterable<array{\DateTimeImmutable, string}>
     */
    public static function provideDateTimesAndExpectedStringRepresentationsData(): iterable
    {
        $dateTimeStrings = [
            '2018-12-10 12:00:00' => '2018-12-10',
            '2019-01-20 12:00:00' => '2019-01-20',
            '2018-10-01 12:00:00' => '2018-10-01',
        ];

        foreach ($dateTimeStrings as $dateTimeString => $expectedStringRepresentation) {
            yield [
                DateTimeFactory::immutableFromFormat('Y-m-d H:i:s', $dateTimeString),
                $expectedStringRepresentation,
            ];
            yield [
                DateTimeFactory::immutableFromFormat('Y-m-d H:i:s', $dateTimeString),
                $expectedStringRepresentation,
            ];
        }
    }

    #[Test]
    #[DataProvider('provideDateTimesAndExpectedStringRepresentationsData')]
    public function it_can_be_constructed_from_a_date_time_instance(
        \DateTimeImmutable $dateTime,
        string $expectedStringRepresentation
    ): void {
        $result = Date::fromDateTime($dateTime);

        $this->assertSame($expectedStringRepresentation, $result->toYearMonthDayString());
    }

    /**
     * @return iterable<array{string, string}>
     */
    public static function provideDateTimeStringsAndExpectedStringRepresentationsData(): iterable
    {
        yield [
            '2018-12-10',
            '2018-12-10',
        ];
        yield [
            '2019-01-20',
            '2019-01-20',
        ];
        yield [
            '2018-10-01',
            '2018-10-01',
        ];
    }

    #[Test]
    #[DataProvider('provideDateTimeStringsAndExpectedStringRepresentationsData')]
    public function it_can_be_constructed_from_a_formatted_string(
        string $dateTimeString,
        string $expectedStringRepresentation
    ): void {
        $result = Date::fromYearMonthDayString($dateTimeString);

        $this->assertSame($expectedStringRepresentation, $result->toYearMonthDayString());
    }

    /**
     * @return iterable<array{string}>
     */
    public static function provideInvalidYearMonthDayStrings(): iterable
    {
        yield ['2008-01-04-'];
        yield ['-2008-01-04'];
        yield ['foobar'];
        yield ['2024-10-10-10'];
        yield ['2024-10-'];
        yield ['2024-'];
        yield ['2024'];
    }

    #[Test]
    #[DataProvider('provideInvalidYearMonthDayStrings')]
    public function it_throws_when_constructing_from_a_formatted_string_if_invalid_format(string $value): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Failed to parse string as a Y-m-d formatted date.');

        Date::fromYearMonthDayString($value);
    }

    #[Test]
    public function it_throws_when_constructing_from_a_formatted_string_if_invalid_date(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Date 2019-13-1 (Y-m-d) is invalid.');

        Date::fromYearMonthDayString('2019-13-01');
    }

    #[Test]
    public function it_exposes_the_year_month_and_day(): void
    {
        $date = Date::fromYearMonthDay(2019, 1, 10);

        $this->assertSame(2019, $date->year());
        $this->assertSame(1, $date->month());
        $this->assertSame(10, $date->day());
    }

    /**
     * @return iterable<list{\DateTimeZone}>
     */
    public static function provideTimeZonesThatAreNotUtc(): iterable
    {
        yield [new \DateTimeZone('Europe/London')];
        yield [new \DateTimeZone('America/New_York')];
    }

    #[Test]
    #[DataProvider('provideTimeZonesThatAreNotUtc')]
    public function it_can_be_converted_to_a_utc_datetime_immutable_instance(\DateTimeZone $nonUtcTimeZone): void
    {
        $currentDefaultTimezone = \date_default_timezone_get();

        try {
            \date_default_timezone_set($nonUtcTimeZone->getName());

            $date = Date::fromYearMonthDay(2019, 2, 15);
            $dateTimeImmutable = $date->toDateTime();

            $this->assertSame('2019-02-15', $dateTimeImmutable->format('Y-m-d'));
            $this->assertSame('00:00:00', $dateTimeImmutable->format('H:i:s'));
            $this->assertSame('UTC', $dateTimeImmutable->getTimezone()->getName());
        } finally {
            \date_default_timezone_set($currentDefaultTimezone);
        }
    }

    #[Test]
    public function it_equals_other_dates_with_the_same_value(): void
    {
        $date = Date::fromYearMonthDay(2019, 1, 2);
        $dateSameValue = Date::fromYearMonthDay(2019, 1, 2);
        $dateDifferentValue1 = Date::fromYearMonthDay(2019, 1, 3);
        $dateDifferentValue2 = Date::fromYearMonthDay(2019, 2, 2);
        $dateDifferentValue3 = Date::fromYearMonthDay(2018, 1, 2);

        $this->assertTrue($date->equals($dateSameValue));
        $this->assertTrue($dateSameValue->equals($date));
        $this->assertFalse($date->equals($dateDifferentValue1));
        $this->assertFalse($dateDifferentValue1->equals($date));
        $this->assertFalse($date->equals($dateDifferentValue2));
        $this->assertFalse($dateDifferentValue2->equals($date));
        $this->assertFalse($date->equals($dateDifferentValue3));
        $this->assertFalse($dateDifferentValue3->equals($date));
    }

    #[Test]
    #[DataProvider('provideDatesForDayAfterIncrementData')]
    public function it_will_correctly_increment_for_following_day(string $currentDate, string $expectedDate): void
    {
        $this->assertSame(
            $expectedDate,
            Date::fromYearMonthDayString($currentDate)->dayAfter()->toYearMonthDayString()
        );
    }

    /**
     * @return iterable<array{string, string}>
     */
    public static function provideDatesForDayAfterIncrementData(): iterable
    {
        yield ['2019-01-04', '2019-01-05'];
        yield ['2018-12-31', '2019-01-01'];
        yield ['2019-02-28', '2019-03-01'];
        yield ['2020-02-29', '2020-03-01'];
        yield ['2020-04-30', '2020-05-01'];
    }

    #[Test]
    #[DataProvider('provideDatesForDayBeforeIncrementData')]
    public function it_will_correctly_increment_for_previous_day(string $currentDate, string $expectedDate): void
    {
        $this->assertSame(
            $expectedDate,
            Date::fromYearMonthDayString($currentDate)->dayBefore()->toYearMonthDayString()
        );
    }

    /**
     * @return iterable<array{string, string}>
     */
    public static function provideDatesForDayBeforeIncrementData(): iterable
    {
        foreach (self::provideDatesForDayAfterIncrementData() as $args) {
            yield \array_reverse($args);
        }
    }

    #[Test]
    #[DataProvider('provideValuesForBeforeComparisonData')]
    public function it_will_return_correct_values_for_before_comparison(
        string $before,
        string $after,
        bool $expectation
    ): void {
        $this->assertSame(
            $expectation,
            Date::fromYearMonthDayString($before)->isBefore(Date::fromYearMonthDayString($after))
        );
    }

    /**
     * @return iterable<array{string, string, bool}>
     */
    public static function provideValuesForBeforeComparisonData(): iterable
    {
        yield ['2019-01-01', '2019-02-01', true];
        yield ['2020-01-01', '2021-01-01', true];
        yield ['2019-01-01', '2019-01-02', true];
        yield ['2018-01-01', '2018-01-01', false];
        yield ['2019-02-01', '2019-01-01', false];
        yield ['2021-01-01', '2020-01-01', false];
        yield ['2019-01-02', '2019-01-01', false];
    }

    #[Test]
    #[DataProvider('provideValuesForBeforeOrEqualToComparisonData')]
    public function it_will_return_correct_values_for_before_or_equal_to_comparison(
        string $before,
        string $after,
        bool $expectation
    ): void {
        $this->assertSame(
            $expectation,
            Date::fromYearMonthDayString($before)->isBeforeOrEqualTo(Date::fromYearMonthDayString($after))
        );
    }

    /**
     * @return iterable<array{string, string, bool}>
     */
    public static function provideValuesForBeforeOrEqualToComparisonData(): iterable
    {
        yield ['2019-01-01', '2019-02-01', true];
        yield ['2020-01-01', '2021-01-01', true];
        yield ['2019-01-01', '2019-01-02', true];
        yield ['2018-01-01', '2018-01-01', true];
        yield ['2019-02-01', '2019-01-01', false];
        yield ['2021-01-01', '2020-01-01', false];
        yield ['2019-01-02', '2019-01-01', false];
    }

    #[Test]
    #[DataProvider('provideValuesForAfterComparisonData')]
    public function it_will_return_correct_values_for_after_comparison(
        string $before,
        string $after,
        bool $expectation
    ): void {
        $this->assertSame(
            $expectation,
            Date::fromYearMonthDayString($before)->isAfter(Date::fromYearMonthDayString($after))
        );
    }

    /**
     * @return iterable<array{string, string, bool}>
     */
    public static function provideValuesForAfterComparisonData(): iterable
    {
        yield ['2019-01-01', '2019-02-01', false];
        yield ['2020-01-01', '2021-01-01', false];
        yield ['2019-01-01', '2019-01-02', false];
        yield ['2018-01-01', '2018-01-01', false];
        yield ['2019-02-01', '2019-01-01', true];
        yield ['2021-01-01', '2020-01-01', true];
        yield ['2019-01-02', '2019-01-01', true];
    }

    #[Test]
    #[DataProvider('provideValuesForAfterOrEqualToComparisonData')]
    public function it_will_return_correct_values_for_after_or_equal_to_comparison(
        string $before,
        string $after,
        bool $expectation
    ): void {
        $this->assertSame(
            $expectation,
            Date::fromYearMonthDayString($before)->isAfterOrEqualTo(Date::fromYearMonthDayString($after))
        );
    }

    /**
     * @return iterable<array{string, string, bool}>
     */
    public static function provideValuesForAfterOrEqualToComparisonData(): iterable
    {
        yield ['2019-01-01', '2019-02-01', false];
        yield ['2020-01-01', '2021-01-01', false];
        yield ['2019-01-01', '2019-01-02', false];
        yield ['2018-01-01', '2018-01-01', true];
        yield ['2019-02-01', '2019-01-01', true];
        yield ['2021-01-01', '2020-01-01', true];
        yield ['2019-01-02', '2019-01-01', true];
    }

    #[Test]
    public function it_will_return_correct_values_for_between_comparison(): void
    {
        $startDate = Date::fromYearMonthDay(2019, 6, 28);
        $endDate = Date::fromYearMonthDay(2019, 7, 28);

        $this->assertFalse(Date::fromYearMonthDay(2019, 6, 27)->isBetween($startDate, $endDate));
        $this->assertTrue(Date::fromYearMonthDay(2019, 6, 28)->isBetween($startDate, $endDate));
        $this->assertTrue(Date::fromYearMonthDay(2019, 7, 13)->isBetween($startDate, $endDate));
        $this->assertTrue(Date::fromYearMonthDay(2019, 7, 28)->isBetween($startDate, $endDate));
        $this->assertFalse(Date::fromYearMonthDay(2019, 7, 29)->isBetween($startDate, $endDate));
    }

    #[Test]
    #[DataProvider('provideDatesAndDayCountsForDiffComparisonData')]
    public function it_will_calculate_difference_between_two_days(
        string $start,
        string $end,
        int $numberOfDays
    ): void {
        $this->assertSame(
            $numberOfDays,
            Date::fromYearMonthDayString($start)->differenceInDays(Date::fromYearMonthDayString($end))
        );
    }

    /**
     * @return iterable<array{string, string, int}>
     */
    public static function provideDatesAndDayCountsForDiffComparisonData(): iterable
    {
        yield ['2020-02-24', '2020-03-03', 8];
        yield ['2019-02-24', '2019-03-03', 7];
        yield ['2018-01-31', '2018-02-28', 28];
    }

    #[Test]
    public function it_calculates_diff_correctly(): void
    {
        $dateTime1 = new \DateTimeImmutable('2020-08-15 00:00:00');
        $dateTime2 = new \DateTimeImmutable('2020-03-15 00:00:00');
        $date1 = Date::fromDateTime($dateTime1);
        $date2 = Date::fromDateTime($dateTime2);

        $expectedDiff = $dateTime1->diff($dateTime2);
        $diff = $date1->diff($date2);

        $this->assertSame($expectedDiff->days, $diff->days);
    }

    /**
     * @return iterable<array{int}>
     */
    public static function provideNumberOfDaysOffsets(): iterable
    {
        yield [-10];
        yield [-5];
        yield [-1];
        yield [0];
        yield [1];
        yield [5];
        yield [10];
    }

    #[Test]
    #[DataProvider('provideNumberOfDaysOffsets')]
    public function it_can_be_offset_by_days(int $days): void
    {
        $date = Date::fromYearMonthDay(2022, 5, 1);

        $offsetDate = $date->offsetByDays($days);

        $expectedDate = Date::fromDateTime($date->toDateTime()->modify(\sprintf('%d days', $days)));

        if ($days !== 0) {
            $this->assertNotSame($date, $offsetDate);
        }

        $this->assertTrue($expectedDate->equals($offsetDate));
    }

    /**
     * @return iterable<array{int}>
     */
    public static function provideNumberOfMonthsOffsets(): iterable
    {
        yield [-10];
        yield [-5];
        yield [-1];
        yield [0];
        yield [1];
        yield [5];
        yield [10];
    }

    #[Test]
    #[DataProvider('provideNumberOfMonthsOffsets')]
    public function it_can_be_offset_by_months(int $days): void
    {
        $date = Date::fromYearMonthDay(2022, 5, 1);

        $offsetDate = $date->offsetByMonths($days);

        $expectedDate = Date::fromDateTime($date->toDateTime()->modify(\sprintf('%d months', $days)));

        if ($days !== 0) {
            $this->assertNotSame($date, $offsetDate);
        }

        $this->assertTrue($expectedDate->equals($offsetDate));
    }

    /**
     * @return iterable<array{int}>
     */
    public static function provideNumberOfYearsOffsets(): iterable
    {
        yield [-10];
        yield [-5];
        yield [-1];
        yield [0];
        yield [1];
        yield [5];
        yield [10];
    }

    #[Test]
    #[DataProvider('provideNumberOfYearsOffsets')]
    public function it_can_be_offset_by_years(int $days): void
    {
        $date = Date::fromYearMonthDay(2022, 5, 1);

        $offsetDate = $date->offsetByYears($days);

        $expectedDate = Date::fromDateTime($date->toDateTime()->modify(\sprintf('%d years', $days)));

        if ($days !== 0) {
            $this->assertNotSame($date, $offsetDate);
        }

        $this->assertTrue($expectedDate->equals($offsetDate));
    }

    #[Test]
    public function it_returns_start_of_day_in_default_timezone(): void
    {
        $startOfDay = Date::fromYearMonthDay(2022, 5, 1)->startOfDay();

        $this->assertSame('2022-05-01 00:00:00.000000', $startOfDay->format('Y-m-d H:i:s.u'));
        $this->assertSame(\date_default_timezone_get(), $startOfDay->getTimezone()->getName());
    }

    #[Test]
    public function it_returns_start_of_day_in_provided_timezone(): void
    {
        $startOfDay = Date::fromYearMonthDay(2022, 5, 1)->startOfDay(new \DateTimeZone('Pacific/Wake'));

        $this->assertSame('2022-05-01 00:00:00.000000', $startOfDay->format('Y-m-d H:i:s.u'));
        $this->assertSame('Pacific/Wake', $startOfDay->getTimezone()->getName());
    }

    #[Test]
    public function it_returns_end_of_day_in_default_timezone(): void
    {
        $endOfDay = Date::fromYearMonthDay(2022, 5, 1)->endOfDay();

        $this->assertSame('2022-05-01 23:59:59.999999', $endOfDay->format('Y-m-d H:i:s.u'));
        $this->assertSame(\date_default_timezone_get(), $endOfDay->getTimezone()->getName());
    }

    #[Test]
    public function it_returns_end_of_day_in_provided_timezone(): void
    {
        $endOfDay = Date::fromYearMonthDay(2022, 5, 1)->endOfDay(new \DateTimeZone('Indian/Mauritius'));

        $this->assertSame('2022-05-01 23:59:59.999999', $endOfDay->format('Y-m-d H:i:s.u'));
        $this->assertSame('Indian/Mauritius', $endOfDay->getTimezone()->getName());
    }
}
