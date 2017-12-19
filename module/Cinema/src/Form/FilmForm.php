<?php

declare(strict_types=1);

namespace Cinema\Form;

use Zend\Form\Element\Text;
use Zend\Form\Form;

class FilmForm extends Form
{
    public function __construct()
    {
        parent::__construct('film');

        $title = new Text('title');
        $title->setLabel('Title');
        $this->add($title);
    }
}
