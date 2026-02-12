<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Config\MonologConfig;

/** @psalm-suppress UndefinedClass */
return static function (MonologConfig $monolog): void {
    $monolog->channels(['webgriffe_sylius_clerk_plugin']);
};
