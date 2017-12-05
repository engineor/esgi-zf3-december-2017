<?php

declare(strict_types=1);

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

final class PingController extends AbstractActionController
{
    /**
     * @var \DateTimeImmutable
     */
    private $dateTime;

    public function __construct(\DateTimeImmutable $dateTime)
    {
        $this->dateTime = $dateTime;
    }

    public function pingAction() : ViewModel
    {
        return new ViewModel([
            'date' => $this->dateTime,
        ]);
    }
}
