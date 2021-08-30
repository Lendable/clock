<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Core\ValueObject\PhpVersion;
use Rector\Set\ValueObject\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::PATHS, [__DIR__.'/lib', __DIR__.'/tests']);
    $parameters->set(Option::PHP_VERSION_FEATURES, PhpVersion::PHP_74);
    $parameters->set(Option::PHPSTAN_FOR_RECTOR_PATH, __DIR__.'/phpstan.neon');

    $containerConfigurator->import(SetList::CODE_QUALITY);
    $containerConfigurator->import(SetList::PHP_74);
};
