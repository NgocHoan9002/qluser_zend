<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Signin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\Result;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\Session as SessionStorage;
use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
use Signin\Form\SigninForm;
use Signin\Model\SigninUserModel;

/**
 * Description of SigninController
 *
 * @author loc
 */
class SigninController extends AbstractActionController {

    //put your code here

    protected $signinTable;

    public function indexAction() {
        $auth = new AuthenticationService();
        if (!$auth->hasIdentity()) {
            return $this->redirect()->toRoute('signin', array('action' => 'signin'));
        } else {
            return $this->redirect()->toRoute('user', array('action' => 'index'));
        }
    }

    public function signinAction() {
        $user = $this->identity();
        $auth = new AuthenticationService();
        if (!$auth->hasIdentity()) {
            $messages = null;
            $form = new SigninForm();
            $request = $this->getRequest();

            if ($request->isPost()) {
                $signinFormFilter = new SigninUserModel();
                $form->setInputFilter($signinFormFilter->getInputFilter());
                $form->setData($request->getPost());
                if ($form->isValid()) {
                    $data = $form->getData();
                    $sm = $this->getServiceLocator();
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

                    $config = $this->getServiceLocator()->get('Config');

                    $authAdapter = new AuthAdapter($dbAdapter, 'user', 'email', 'password');

                    $authAdapter->setIdentity($data['email']);
                    $authAdapter->setCredential(md5($data['password']));

                    $result = $auth->authenticate($authAdapter);

                    switch ($result->getCode()) {
                        case Result::FAILURE_IDENTITY_NOT_FOUND:
                            break;
                        case Result::FAILURE_CREDENTIAL_INVALID:
                            break;
                        case Result::SUCCESS:
                            $storage = $auth->getStorage();
                            $storage->write($authAdapter->getResultRowObject(null, 'password'));
                            $time = 604800; //7 days
                            if ($data['rememberme']) {
                                $sessionManager = new \Zend\Session\SessionManager();
                                $sessionManager->rememberMe($time);
                            }
                            return $this->redirect()->toRoute('user',array('action'=>'index'));
                        default :
                            break;
                    }

                    foreach ($result->getMessages() as $message) {
                        $messages.="$message\n";
                    }
                }
            }
            return new ViewModel(array('form' => $form, 'messages' => $messages));
        } else {
            return $this->redirect()->toRoute('user',array('action'=>'index'));
        }

//        $user_session = new \Zend\Session\Container('user');
//        if ($user_session->email!=null) {
//            return $this->redirect()->toRoute('user',array('action'=>'index'));
//        }
//        $form = new SigninForm();
//        $item = new SigninUserModel();
//        $request = $this->getRequest();
//        if ($request->isPost()) {
//            $form->setInputFilter($item->getInputFilter());
//            $form->setData($request->getPost());
//            if ($form->isValid()) {
//                $item->email = $form->get('email')->getValue();
//                $item->password = $form->get('password')->getValue();
//                $success = $this->getTable()->signin($item);
//                if ($success) {
//                    $user_session = new \Zend\Session\Container('user');
//                    $user_session->email=$success->email;
//                    $user_session->role=$success->role;
//                    return $this->redirect()->toRoute('user');
//                } else {
//                    $error='Wrong email or password';
//                }
//            }
//        }
//        
//        return array(
//            'form'=>$form,
//            'error'=>$error
//        );
    }

    public function logoutAction() {
        $auth = new AuthenticationService();
        if ($auth->hasIdentity()) {
            $identity=$auth->getIdentity(); 
        }
        $auth->clearIdentity();
//        $sessionManager = new \Zend\Session\SessionManager();
//        $sessionManager->forgetMe();
        return $this->redirect()->toRoute('signin',array('action'=>'index'));
    }
//    public function getTable() {
//        if (!$this->signinTable) {
//            $sm = $this->getServiceLocator();
//            $this->signinTable = $sm->get('Signin\Model\SigninTable');
//        }
//        return $this->signinTable;
//    }
}
