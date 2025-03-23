<?php

declare(strict_types=1);

namespace Tests\Lendable\Clock\Unit\Bridge\Doctrine\Type;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Exception\InvalidFormat;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Exception\ValueNotConvertible;
use Lendable\Clock\Bridge\Doctrine\Type\DateType;
use Lendable\Clock\Date;
use Lendable\PHPUnitExtensions\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RequiresMethod;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(DateType::class)]
final class DateTypeTest extends TestCase
{
    #[Test]
    public function null_conversion_to_db_value(): void
    {
        $this->assertNull((new DateType())->convertToDatabaseValue(null, new MySQLPlatform()));
    }

    #[Test]
    public function conversion_to_db_value(): void
    {
        $this->assertSame(
            '2020-01-03',
            (new DateType())->convertToDatabaseValue(Date::fromYearMonthDayString('2020-01-03'), new MySQLPlatform()),
        );
    }

    #[Test]
    public function db_value_conversion(): void
    {
        $this->assertSame(
            '2020-01-03',
            (new DateType())->convertToPHPValue('2020-01-03', new MySQLPlatform())?->toYearMonthDayString(),
        );
    }

    #[Test]
    public function null_db_value_conversion(): void
    {
        $this->assertNull((new DateType())->convertToPHPValue(null, new MySQLPlatform()));
    }

    #[Test]
    #[RequiresMethod(ValueNotConvertible::class, 'new')]
    public function invalid_db_value_conversion_dbal_4(): void
    {
        $this->expectExceptionObject(ValueNotConvertible::new(321, DateType::NAME));

        (new DateType())->convertToPHPValue(321, new MySQLPlatform());
    }

    #[Test]
    #[RequiresMethod(InvalidFormat::class, 'new')]
    public function invalid_db_format_conversion_dbal_4(): void
    {
        $this->expectExceptionObject(InvalidFormat::new('2010-01', DateType::NAME, 'Y-m-d'));

        try {
            (new DateType())->convertToPHPValue('2010-01', new MySQLPlatform());
        } catch (InvalidFormat $e) {
            $this->assertInstanceOf(\InvalidArgumentException::class, $e->getPrevious());

            throw $e;
        }
    }

    #[Test]
    #[RequiresMethod(InvalidType::class, 'new')]
    public function invalid_php_value_conversion_dbal_4(): void
    {
        $this->expectExceptionObject(InvalidType::new(321, DateType::NAME, ['null', Date::class]));

        (new DateType())->convertToDatabaseValue(321, new MySQLPlatform());
    }

    #[Test]
    #[RequiresMethod(ConversionException::class, 'conversionFailed')]
    public function invalid_db_value_conversion_dbal_3(): void
    {
        $this->expectExceptionObject(ConversionException::conversionFailed(321, DateType::NAME));

        (new DateType())->convertToPHPValue(321, new MySQLPlatform());
    }

    #[Test]
    #[RequiresMethod(ConversionException::class, 'conversionFailedFormat')]
    public function invalid_db_format_conversion_dbal_3(): void
    {
        $this->expectExceptionObject(ConversionException::conversionFailedFormat('2010-01', DateType::NAME, 'Y-m-d'));

        try {
            (new DateType())->convertToPHPValue('2010-01', new MySQLPlatform());
        } catch (ConversionException $e) {
            $this->assertInstanceOf(\InvalidArgumentException::class, $e->getPrevious());

            throw $e;
        }
    }

    #[Test]
    #[RequiresMethod(ConversionException::class, 'conversionFailedInvalidType')]
    public function invalid_php_value_conversion_dbal_3(): void
    {
        $this->expectExceptionObject(ConversionException::conversionFailedInvalidType(321, DateType::NAME, ['null', Date::class]));

        (new DateType())->convertToDatabaseValue(321, new MySQLPlatform());
    }
}
