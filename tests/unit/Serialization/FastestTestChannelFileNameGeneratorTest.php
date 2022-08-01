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
        $this->exerciseAndResetEnv(function (): void {
            \putenv(\sprintf('%s=3', EnvCommandCreator::ENV_TEST_CHANNEL_READABLE));
            $this->assertSame('now_3.json', (new FastestTestChannelFileNameGenerator())->generate());
        });
    }

    /**
     * @test
     */
    public function it_defaults_to_1_when_the_env_var_is_not_provided(): void
    {
        $this->exerciseAndResetEnv(function (): void {
            \putenv(EnvCommandCreator::ENV_TEST_CHANNEL_READABLE);
            $this->assertSame('now_1.json', (new FastestTestChannelFileNameGenerator())->generate());
        });
    }

    /**
     * @param callable(): void $code
     */
    private function exerciseAndResetEnv(callable $code): void
    {
        $old = \getenv(EnvCommandCreator::ENV_TEST_CHANNEL_READABLE);

        try {
            $code();
        } finally {
            if (\is_string($old)) {
                \putenv(\sprintf('%s=%s', EnvCommandCreator::ENV_TEST_CHANNEL_READABLE, $old));
            } else {
                \putenv(\sprintf('%s=', EnvCommandCreator::ENV_TEST_CHANNEL_READABLE));
            }
        }
    }
}
