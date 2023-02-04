<?php

declare(strict_types=1);

namespace Lendable\Clock\Serialization;

/**
 * Always generates the same configurable file name.
 */
final class FixedFileNameGenerator implements FileNameGenerator
{
    public function __construct(private readonly string $fileName = 'now.json')
    {
    }

    public function generate(): string
    {
        return $this->fileName;
    }
}
