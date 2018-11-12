<?php

declare(strict_types=1);

namespace Tests\Lendable\Clock\Unit\Serialization;

use Lendable\Clock\Serialization\FixedFileNameGenerator;
use PHPUnit\Framework\TestCase;

final class FixedFileNameGeneratorTest extends TestCase
{
    /**
     * @test
     */
    public function it_generates_the_fixed_given_file_name(): void
    {
        $this->assertSame('foo.json', (new FixedFileNameGenerator('foo.json'))->generate());
    }

    /**
     * @test
     */
    public function it_defaults_to_a_name(): void
    {
        $this->assertSame('now.json', (new FixedFileNameGenerator())->generate());
    }
}
