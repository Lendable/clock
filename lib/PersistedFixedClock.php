<?php

declare(strict_types=1);

namespace Lendable\Clock;

use Lendable\Clock\Serialization\FileNameGenerator;

/**
 * Stores the provided current time to disk to persist between process executions.
 *
 * The only real use case for such scenario is functional testing.
 */
final class PersistedFixedClock implements MutableClock
{
    private const SERIALIZATION_FORMAT = 'Y-m-d\TH:i:s.u';

    private FixedClock $delegate;

    private function __construct(private string $serializedStorageDirectory, private FileNameGenerator $fileNameGenerator)
    {
    }

    public static function fromPersisted(string $serializedStorageDirectory, FileNameGenerator $fileNameGenerator): self
    {
        $instance = new self($serializedStorageDirectory, $fileNameGenerator);
        $instance->load();

        return $instance;
    }

    public static function initializeWith(string $serializedStorageDirectory, FileNameGenerator $fileNameGenerator, \DateTimeImmutable $now): self
    {
        $instance = new self($serializedStorageDirectory, $fileNameGenerator);
        $instance->delegate = new FixedClock($now);
        $instance->persist();

        return $instance;
    }

    public function now(): \DateTimeImmutable
    {
        return $this->delegate->now();
    }

    public function nowMutable(): \DateTime
    {
        return $this->delegate->nowMutable();
    }

    public function today(): Date
    {
        return Date::fromDateTime($this->now());
    }

    public function changeTimeTo(\DateTimeInterface $time): void
    {
        $this->delegate->changeTimeTo($time);
        $this->persist();
    }

    private function load(): void
    {
        $path = $this->getSerializationFilePath();
        $contents = \file_get_contents($path);
        \assert(\is_string($contents));
        $data = \json_decode($contents, true, 512, \JSON_THROW_ON_ERROR);

        if (!\is_array($data)) {
            throw new \RuntimeException(
                \sprintf(
                    'Expected data to decode to an array, but got %s.',
                    \get_debug_type($data)
                )
            );
        }

        if (!isset($data['timestamp'], $data['timezone'])) {
            throw new \RuntimeException(
                \sprintf(
                    'Expected to decode to an associative array containing keys timestamp and timezone. Got keys [%s].',
                    \implode(', ', \array_map(static fn ($k): string => '"'.$k.'"', \array_keys($data)))
                )
            );
        }

        $now = \DateTimeImmutable::createFromFormat(
            self::SERIALIZATION_FORMAT,
            $data['timestamp'],
            new \DateTimeZone($data['timezone'])
        );
        \assert($now instanceof \DateTimeImmutable);

        $this->delegate = new FixedClock($now);
    }

    private function persist(): void
    {
        $now = $this->delegate->now();

        \file_put_contents(
            $this->getSerializationFilePath(),
            \json_encode(
                [
                    'timestamp' => $now->format(self::SERIALIZATION_FORMAT),
                    'timezone' => $now->getTimezone()->getName(),
                ],
                \JSON_THROW_ON_ERROR
            )
        );
    }

    private function getSerializationFilePath(): string
    {
        return $this->serializedStorageDirectory.'/'.$this->fileNameGenerator->generate();
    }
}
