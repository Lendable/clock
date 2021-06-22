<?php

declare(strict_types=1);

namespace Lendable\Clock\Serialization;

/**
 * Always generates the same configurable file name.
 */
final class FixedFileNameGenerator implements FileNameGenerator
{
    private string $fileName;

    public function __construct(string $fileName = 'now.json')
    {
        $this->fileName = $fileName;
    }

    public function generate(): string
    {
        return $this->fileName;
    }
}
