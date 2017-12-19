<?php

declare(strict_types=1);

namespace Cinema\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

final class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel([
            'films' => [],
        ]);
    }
}
