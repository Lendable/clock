<?php

declare(strict_types=1);

namespace Lendable\Clock\Serialization;

interface FileNameGenerator
{
    public function generate(): string;
}
