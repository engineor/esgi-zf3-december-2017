<?php

declare(strict_types=1);

namespace Application\Controller;

final class PingControllerFactory
{
    public function __invoke() : PingController
    {
        $dateTime = new \DateTimeImmutable();

        return new PingController($dateTime);
    }
}
