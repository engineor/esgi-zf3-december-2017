<?php

declare(strict_types=1);

namespace Cinema\Controller;

use Cinema\Entity\Film;
use Cinema\Form\FilmForm;
use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;

final class IndexControllerFactory
{
    public function __invoke(ContainerInterface $container) : IndexController
    {
        $filmRepository = $container->get(EntityManager::class)->getRepository(Film::class);
        $filmForm = $container->get(FilmForm::class);

        return new IndexController($filmRepository, $filmForm);
    }
}
