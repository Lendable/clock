<?php

declare(strict_types=1);

namespace Lendable\Clock;

/**
 * Normalizes date time instances to either an immutable variant or a mutable variant.
 *
 * If a mutable variant is required, a new instance will be returned.
 *
 * While the \DateTimeInterface exists, it is inherently hard to reason with. It exposes APIs
 * that either update the instance's state or don't, depending on the underlying implementation.
 * This tends to make it safe to only consume in your public API, and then immediately convert
 * and handle internally as one of the two concrete types.
 */
final class DateTimeNormalizer
{
    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * Normalizes a time to an immutable variant.
     */
    public static function immutable(\DateTimeInterface $time): \DateTimeImmutable
    {
        // TODO Once minimum PHP version is >= 8.0, this can be reduced down to \DateTimeImmutable::createFromInterface().

        if ($time instanceof \DateTimeImmutable) {
            return $time;
        }

        if (\PHP_VERSION_ID >= 80000) {
            return \DateTimeImmutable::createFromInterface($time);
        }

        if ($time instanceof \DateTime) {
            return \DateTimeImmutable::createFromMutable($time);
        }

        // @codeCoverageIgnoreStart
        throw self::createExceptionForInvalidImplementation($time);
        // @codeCoverageIgnoreEnd
    }

    public static function mutable(\DateTimeInterface $time): \DateTime
    {
        // TODO Once minimum PHP version is >= 8.0, this can be reduced down to \DateTime::createFromInterface().

        if (\PHP_VERSION_ID >= 80000) {
            return \DateTime::createFromInterface($time);
        }

        if ($time instanceof \DateTime) {
            return clone $time;
        }

        if ($time instanceof \DateTimeImmutable) {
            return \DateTime::createFromImmutable($time);
        }

        // @codeCoverageIgnoreStart
        throw self::createExceptionForInvalidImplementation($time);
        // @codeCoverageIgnoreEnd
    }

    /**
     * @codeCoverageIgnore
     */
    private static function createExceptionForInvalidImplementation(\DateTimeInterface $time): \LogicException
    {
        return new \LogicException(
            \sprintf(
                'This should not happen, expected an instance of %s or %s, got %s as a %s implementation.',
                \DateTimeImmutable::class,
                \DateTime::class,
                \get_class($time),
                \DateTimeInterface::class
            )
        );
    }
}