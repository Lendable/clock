<?php

declare(strict_types=1);

namespace Lendable\Clock\Bridge\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Exception\InvalidFormat;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Exception\ValueNotConvertible;
use Doctrine\DBAL\Types\Type;
use Lendable\Clock\Date;

final class DateType extends Type
{
    public const NAME = 'lendable_date';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getDateTypeDeclarationSQL($column);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Date
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw \class_exists(ValueNotConvertible::class)
                ? ValueNotConvertible::new($value, self::NAME)
                : ConversionException::conversionFailed($value, self::NAME);
        }

        try {
            return Date::fromYearMonthDayString($value);
        } catch (\InvalidArgumentException $e) {
            throw \class_exists(InvalidFormat::class)
                ? InvalidFormat::new($value, self::NAME, 'Y-m-d', $e)
                : ConversionException::conversionFailedFormat($value, self::NAME, 'Y-m-d', $e);
        }
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Date) {
            return $value->toYearMonthDayString();
        }

        throw \class_exists(InvalidType::class)
            ? InvalidType::new($value, self::NAME, ['null', Date::class])
            : ConversionException::conversionFailedInvalidType(
                $value,
                self::NAME,
                ['null', Date::class],
            );
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
