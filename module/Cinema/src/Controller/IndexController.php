<?php

declare(strict_types=1);

namespace Cinema\Controller;

use Doctrine\ORM\EntityRepository;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

final class IndexController extends AbstractActionController
{
    /**
     * @var EntityRepository
     */
    private $filmRepository;

    public function __construct(EntityRepository $filmRepository)
    {
        $this->filmRepository = $filmRepository;
    }

    public function indexAction()
    {
        return new ViewModel([
            'films' => $this->filmRepository->findAll(),
        ]);
    }
}
