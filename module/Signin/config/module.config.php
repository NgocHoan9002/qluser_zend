<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'Signin\Controller\Signin' => 'Signin\Controller\SigninController'
        ),
    ),
    'router' => array(
        'routes' => array(
            'signin' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/signin[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Signin\Controller\Signin',
                        'action' => 'index',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'signin' => __DIR__ . '/../view',
        ),
    ),
    'service_manager' => array(
// added for Authentication and Authorization. Without this each time we have to create a new instance.
// This code should be moved to a module to allow Doctrine to overwrite it
        'aliases' => array(// !!! aliases not alias
            'Zend\Authentication\AuthenticationService' => 'my_auth_service',
        ),
        'invokables' => array(
            'my_auth_service' => 'Zend\Authentication\AuthenticationService',
        ),
    ),
);
