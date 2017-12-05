<?php

declare(strict_types=1);

namespace Application\Controller;

use Psr\Container\ContainerInterface;

final class PingControllerFactory
{
    public function __invoke(ContainerInterface $container) : PingController
    {
        $dateTime = $container->get(\DateTimeImmutable::class);

        return new PingController($dateTime);
    }
}
