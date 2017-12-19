<?php

declare(strict_types=1);

namespace Cinema\Controller;

use Application\Repository\FilmRepository;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

final class IndexController extends AbstractActionController
{
    /**
     * @var FilmRepository
     */
    private $filmRepository;

    public function __construct(FilmRepository $filmRepository)
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
