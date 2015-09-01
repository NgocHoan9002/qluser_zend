<?php
namespace Signin;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Signin\Model\SigninUserModel;
use Signin\Model\SigninTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use User\Model\User;
use Zend\Session\Config\SessionConfig;
use Zend\Session\SessionManager;
use Zend\Session\Container;
use Zend\EventManager\EventInterface;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface
{
    public function onBootstrap(EventInterface $evm)
    {
//        $eventManager        = $e->getApplication()->getEventManager();
//        $moduleRouteListener = new ModuleRouteListener();
//        $moduleRouteListener->attach($eventManager);
//        $this->bootstrapSession($e);
//        
//        $config = $evm->getApplication()
//                  ->getServiceManager()
//                  ->get('Configuration');
//
//        $sessionConfig = new SessionConfig();
//        $sessionConfig->setOptions($config['session']);
//        $sessionManager = new SessionManager($sessionConfig);
//        $sessionManager->start();
//        Container::setDefaultManager($sessionManager);
    }
    
//    public function bootstrapSession($e)
//    {
//        $session = $e->getApplication()
//                     ->getServiceManager()
//                     ->get('Zend\Session\SessionManager');
//        $session->start();
//
//        $container = new Container('initialized');
//        if (!isset($container->init)) {
//            $serviceManager = $e->getApplication()->getServiceManager();
//            $request        = $serviceManager->get('Request');
//
//            $session->regenerateId(true);
//            $container->init          = 1;
//            $container->remoteAddr    = $request->getServer()->get('REMOTE_ADDR');
//            $container->httpUserAgent = $request->getServer()->get('HTTP_USER_AGENT');
//
//            $config = $serviceManager->get('Config');
//            if (!isset($config['session'])) {
//                return;
//            }
//
//            $sessionConfig = $config['session'];
//            if (isset($sessionConfig['validators'])) {
//                $chain   = $session->getValidatorChain();
//
//                foreach ($sessionConfig['validators'] as $validator) {
//                    switch ($validator) {
//                        case 'Zend\Session\Validator\HttpUserAgent':
//                            $validator = new $validator($container->httpUserAgent);
//                            break;
//                        case 'Zend\Session\Validator\RemoteAddr':
//                            $validator  = new $validator($container->remoteAddr);
//                            break;
//                        default:
//                            $validator = new $validator();
//                    }
//
//                    $chain->attach('session.validate', array($validator, 'isValid'));
//                }
//            }
//        }
//    }
    
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    
    public function getServiceConfig() {
        return array(
            'factories' => array(
                'Signin\Model\SigninTable' => function($sm) {
                    $tableGateway = $sm->get('SigninTableGateway');
                    $table = new SigninTable($tableGateway);
                    return $table;
                },
                'SigninTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new User());
                    return new TableGateway('user', $dbAdapter, null, $resultSetPrototype);
                },
                'Zend\Session\SessionManager' => function ($sm) {
                    $config = $sm->get('config');
                    if (isset($config['session'])) {
                        $session = $config['session'];

                        $sessionConfig = null;
                        if (isset($session['config'])) {
                            $class = isset($session['config']['class'])  ? $session['config']['class'] : 'Zend\Session\Config\SessionConfig';
                            $options = isset($session['config']['options']) ? $session['config']['options'] : array();
                            $sessionConfig = new $class();
                            $sessionConfig->setOptions($options);
                        }

                        $sessionStorage = null;
                        if (isset($session['storage'])) {
                            $class = $session['storage'];
                            $sessionStorage = new $class();
                        }

                        $sessionSaveHandler = null;
                        if (isset($session['save_handler'])) {
                            // class should be fetched from service manager since it will require constructor arguments
                            $sessionSaveHandler = $sm->get($session['save_handler']);
                        }

                        $sessionManager = new SessionManager($sessionConfig, $sessionStorage, $sessionSaveHandler);
                    } else {
                        $sessionManager = new SessionManager();
                    }
                    Container::setDefaultManager($sessionManager);
                    return $sessionManager;
                },
            ),
        );
    }
}