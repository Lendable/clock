<?php

declare(strict_types=1);

namespace Lendable\Clock;

/**
 * Provides the ability to construct {@see \DateTime} and {@see \DateTimeImmutable} instances with an API that does
 * not return false on failure.
 */
final class DateTimeFactory
{
    private function __construct()
    {
    }

    /**
     * Creates a {@see \DateTimeImmutable} instance from a formatted string in a given timezone (or the default system
     * timezone).
     *
     * @throws \InvalidArgumentException If an instance cannot be created for the given format and value.
     */
    public static function immutableFromFormat(string $format, string $value, ?\DateTimeZone $timezone = null): \DateTimeImmutable
    {
        $instance = \DateTimeImmutable::createFromFormat($format, $value, $timezone ?? new \DateTimeZone(\date_default_timezone_get()));

        if ($instance === false) {
            throw new \InvalidArgumentException(
                \sprintf(
                    'Cannot create \DateTimeImmutable instance from format "%s" and value "%s".',
                    $format,
                    $value,
                )
            );
        }

        return $instance;
    }

    /**
     * Creates a {@see \DateTimeImmutable} instance from a formatted string in a given timezone (or the default system
     * timezone).
     *
     * @throws \InvalidArgumentException If an instance cannot be created for the given format and value.
     */
    public static function mutableFromFormat(string $format, string $value, ?\DateTimeZone $timezone = null): \DateTime
    {
        $instance = \DateTime::createFromFormat($format, $value, $timezone ?? new \DateTimeZone(\date_default_timezone_get()));

        if ($instance === false) {
            throw new \InvalidArgumentException(
                \sprintf(
                    'Cannot create \DateTime instance from format "%s" and value "%s".',
                    $format,
                    $value,
                )
            );
        }

        return $instance;
    }
}
