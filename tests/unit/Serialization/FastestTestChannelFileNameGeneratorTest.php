<?php

declare(strict_types=1);

namespace Tests\Lendable\Clock\Unit\Serialization;

use Lendable\Clock\Serialization\FastestTestChannelFileNameGenerator;
use Liuggio\Fastest\Process\EnvCommandCreator;
use PHPUnit\Framework\TestCase;

final class FastestTestChannelFileNameGeneratorTest extends TestCase
{
    /**
     * @test
     */
    public function it_bases_the_file_name_off_the_env_var_exposed_by_fastest(): void
    {
        $old = \getenv(EnvCommandCreator::ENV_TEST_CHANNEL_READABLE);
        \putenv(\sprintf('%s=3', EnvCommandCreator::ENV_TEST_CHANNEL_READABLE));

        try {
            $this->assertSame('now_3.json', (new FastestTestChannelFileNameGenerator())->generate());
        } finally {
            if (\is_string($old)) {
                \putenv(\sprintf('%s=%s', EnvCommandCreator::ENV_TEST_CHANNEL_READABLE, $old));
            } else {
                \putenv(\sprintf('%s=', EnvCommandCreator::ENV_TEST_CHANNEL_READABLE));
            }
        }
    }
}
