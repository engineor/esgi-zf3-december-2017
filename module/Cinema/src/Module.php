<?php

declare(strict_types=1);

namespace Cinema;

use Zend\ModuleManager\Feature\ConfigProviderInterface;

final class Module implements ConfigProviderInterface
{
    public function getConfig() : array
    {
        return require __DIR__.'/../config/module.config.php';
    }
}
