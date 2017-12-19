<?php

declare(strict_types=1);

namespace Cinema\Controller;

use Application\Repository\FilmRepository;
use Cinema\Form\FilmForm;
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

    /**
     * @var FilmForm
     */
    private $filmForm;

    public function __construct(FilmRepository $filmRepository, FilmForm $filmForm)
    {
        $this->filmRepository = $filmRepository;
        $this->filmForm = $filmForm;
    }

    public function indexAction()
    {
        return new ViewModel([
            'films' => $this->filmRepository->findAll(),
        ]);
    }

    public function addAction()
    {
        $form = $this->filmForm;
        $form->prepare();

        return new ViewModel([
            'form' => $form,
        ]);
    }
}
