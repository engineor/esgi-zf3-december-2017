<?php

declare(strict_types=1);

use Cinema\Form\FilmForm;
use Zend\Router\Http\Literal;
use Cinema\Controller;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'films' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/films',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'add' => [
                        'type' => Literal::class,
                        'options' => [
                            'route'    => '/new',
                            'defaults' => [
                                'action'     => 'add',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => Controller\IndexControllerFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            FilmForm::class => InvokableFactory::class,
        ],
    ],
    'view_manager' => [
        'template_map' => [
            'cinema/index/index' => __DIR__ . '/../view/cinema/index/index.phtml',
            'cinema/index/add' => __DIR__ . '/../view/cinema/index/add.phtml',
        ],
    ],
];
