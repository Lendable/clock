<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Core\ValueObject\PhpVersion;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rector): void {
    $rector->parallel();
    $rector->paths([__DIR__.'/lib', __DIR__.'/tests']);
    $rector->phpVersion(PhpVersion::PHP_81);
    $rector->phpstanConfig(__DIR__.'/phpstan-rector.neon');
    $rector->sets([
        SetList::CODE_QUALITY,
        LevelSetList::UP_TO_PHP_81,
    ]);
};
