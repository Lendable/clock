<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\PHPUnit\Set\PHPUnitSetList;

return RectorConfig::configure()
    ->withPaths([__DIR__.'/lib', __DIR__.'/tests'])
    ->withRootFiles()
    ->withPHPStanConfigs([__DIR__.'/phpstan-rector.neon'])
    ->withPhpSets(php81: true)
    ->withPreparedSets(codeQuality: true)
    ->withAttributesSets(phpunit: true)
    ->withSets([PHPUnitSetList::PHPUNIT_100]);
