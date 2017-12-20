<?php

declare(strict_types=1);

namespace Cinema\Controller;

use Cinema\Entity\Film;
use Cinema\Repository\FilmRepository;
use Cinema\Form\FilmForm;
use Zend\Http\PhpEnvironment\Request;
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

        /* @var $request Request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $film = $this->filmRepository->createFilmFromNameAndDescription(
                    $form->getData()['title'],
                    $form->getData()['description'] ?? ''
                );
                $this->filmRepository->add($film);
                return $this->redirect()->toRoute('films');
            }
        }

        $form->prepare();

        return new ViewModel([
            'form' => $form,
        ]);
    }
}
