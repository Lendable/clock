<?php

declare(strict_types=1);

namespace Lendable\Clock;

use Lendable\Clock\Serialization\FileNameGenerator;

/**
 * Stores the provided current time to disk to persist between process executions.
 *
 * The only real use case for such scenario is functional testing.
 */
final class SerializedFixedClock implements Clock
{
    private const SERIALIZATION_FORMAT = 'Y-m-d\TH:i:s.u';

    /**
     * @var string
     */
    private $serializedStorageDirectory;

    /**
     * @var FileNameGenerator
     */
    private $fileNameGenerator;

    /**
     * @var Clock
     */
    private $delegate;

    private function __construct(string $serializedStorageDirectory, FileNameGenerator $fileNameGenerator)
    {
        if (!\extension_loaded('json')) {
            // @codeCoverageIgnoreStart
            throw new \RuntimeException('ext-json is required to use this class.');
            // @codeCoverageIgnoreEnd
        }

        $this->serializedStorageDirectory = $serializedStorageDirectory;
        $this->fileNameGenerator = $fileNameGenerator;
    }

    public static function fromSerializedData(string $serializedStorageDirectory, FileNameGenerator $fileNameGenerator): self
    {
        $instance = new self($serializedStorageDirectory, $fileNameGenerator);
        $instance->load();
        \assert($instance->delegate instanceof Clock);

        return $instance;
    }

    public static function initializeWith(string $serializedStorageDirectory, FileNameGenerator $fileNameGenerator, \DateTimeImmutable $now): self
    {
        $instance = new self($serializedStorageDirectory, $fileNameGenerator);
        $instance->delegate = new FixedClock($now);
        $instance->save();

        return $instance;
    }

    public function now(): \DateTimeImmutable
    {
        return $this->delegate->now();
    }

    private function load(): void
    {
        $path = $this->getSerializationFilePath();
        $data = \json_decode(\file_get_contents($path), true);
        $this->delegate = new FixedClock(
            \DateTimeImmutable::createFromFormat(
                self::SERIALIZATION_FORMAT,
                $data['timestamp'],
                new \DateTimeZone($data['timezone'])
            )
        );
    }

    private function save(): void
    {
        $now = $this->delegate->now();

        \file_put_contents(
            $this->getSerializationFilePath(),
            \json_encode(
                [
                    'timestamp' => $now->format(self::SERIALIZATION_FORMAT),
                    'timezone' => $now->getTimezone()->getName(),
                ]
            )
        );
    }

    private function getSerializationFilePath(): string
    {
        return $this->serializedStorageDirectory.'/'.$this->fileNameGenerator->generate();
    }
}
