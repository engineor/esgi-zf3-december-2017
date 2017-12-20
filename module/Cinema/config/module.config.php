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
    'doctrine' => [
        'driver' => [
            // defines an annotation driver with two paths, and names it `my_annotation_driver`
            'cinema_driver' => [
                'class' => \Doctrine\ORM\Mapping\Driver\AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [
                    __DIR__.'/../src/Entity/',
                ],
            ],

            // default metadata driver, aggregates all other drivers into a single one.
            // Override `orm_default` only if you know what you're doing
            'orm_default' => [
                'drivers' => [
                    // register `application_driver` for any entity under namespace `Application\Entity`
                    'Cinema\Entity' => 'cinema_driver',
                ],
            ],
        ],
    ],
];
