<?php

declare(strict_types=1);

namespace Application;

use Psr\Container\ContainerInterface;

final class DateTimeImmutableFactory
{
    public function __invoke(ContainerInterface $container) : \DateTimeImmutable
    {
        $config = $container->get('config');
        if (!isset($config['app']['date']) || !is_string($config['app']['date'])) {
            throw new \Exception('Config manquante');
        }

        return new \DateTimeImmutable($config['app']['date']);
    }
}
