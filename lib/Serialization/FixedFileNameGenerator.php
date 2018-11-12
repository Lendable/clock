<?php

declare(strict_types=1);

namespace Lendable\Clock\Serialization;

final class FixedFileNameGenerator implements FileNameGenerator
{
    /**
     * @var string
     */
    private $fileName;

    public function __construct(string $fileName = 'now.json')
    {
        $this->fileName = $fileName;
    }

    public function generate(): string
    {
        return $this->fileName;
    }
}
