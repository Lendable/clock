<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\ClassMethod\DateTimeToDateTimeInterfaceRector;
use Rector\Core\Configuration\Option;
use Rector\Core\ValueObject\PhpVersion;
use Rector\Set\ValueObject\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::PATHS, [__DIR__.'/lib', __DIR__.'/tests']);
    $parameters->set(Option::PHP_VERSION_FEATURES, PhpVersion::PHP_80);
    $parameters->set(Option::PHPSTAN_FOR_RECTOR_PATH, __DIR__.'/phpstan.neon');
    $parameters->set(Option::PARALLEL, true);
    $parameters->set(Option::SKIP, [
        DateTimeToDateTimeInterfaceRector::class,
    ]);

    $containerConfigurator->import(SetList::CODE_QUALITY);
    $containerConfigurator->import(SetList::PHP_74);
    $containerConfigurator->import(SetList::PHP_80);
};
