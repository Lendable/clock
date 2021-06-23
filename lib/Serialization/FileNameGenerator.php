<?php

declare(strict_types=1);

namespace Lendable\Clock\Serialization;

interface FileNameGenerator
{
    /**
     * Generates a file name to persist a serialized version of the current clock time.
     */
    public function generate(): string;
}
