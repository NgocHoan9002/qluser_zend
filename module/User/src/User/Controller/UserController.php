<?php

namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use User\Form\UserForm;
use User\Form\ChangePasswordForm;
use User\Form\LoginForm;
use User\Form\EditUserForm;
use User\Model\User;
use Zend\Form\FormInterface;
use ZendSearch\Lucene;
use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Index;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
use \User\Model\ChangePasswordModel;

//use Zend\Db\Table;

class UserController extends AbstractActionController {

    protected $userTable;
    protected $traceError = true;

     public function getUserTable() {
        if (!$this->userTable) {
            $sm = $this->getServiceLocator();
            $this->userTable = $sm->get('User\Model\UserTable');
        }
        return $this->userTable;
    }
    
    public function indexAction() {
        $auth = new \Zend\Authentication\AuthenticationService();
        if ($auth->getIdentity()) {
            $user = $auth->getIdentity();
        } else {
            return $this->redirect()->toRoute('signin', array('action' => 'index'));
        }
        $paginator = $this->getUserTable()->fetchAll(true);
        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(4);

        return new ViewModel(array(
            'paginator' => $paginator,
            'user' => $user,
        ));
    }

    public function addAction() {
//        $user_session = new \Zend\Session\Container('user');
//        if ($user_session->email == null) {
//            return $this->redirect()->toRoute('signin', array(
//                        'action' => 'signin'
//            ));
//        }
//        if ($user_session->role == 'Operator') {
//            return array('error' => 'You don\'t have permission to do this action');
//        }
        $auth = new \Zend\Authentication\AuthenticationService();
        if ($auth->hasIdentity()) {
            $user = $auth->getIdentity();
        } else {
            return $this->redirect()->toRoute('signin', array('action' => 'index'));
        }
        if ($user->role == 'Operator') {
            //$this->flashMessenger()->addMessage('You don\'t have permission to do this action');
            //$this->redirect()->toRoute('add');
            return array('error' => 'You don\'t have permission to do this action');
        }
        $form = new UserForm(null, $user->role);
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();

        if ($request->isPost()) {
            $user = new User();
            $form->setInputFilter($user->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $user->exchangeArray($form->getData());
                $this->getUserTable()->saveUser($user);
                return $this->redirect()->toRoute('user');
            } else {
                echo "Fail to add new user";
            }
        }
        return array(
            'form' => $form,
            'flashMessages' => $this->flashMessenger()->getMessages(),
        );
    }

    public function editAction() {

        $auth = new \Zend\Authentication\AuthenticationService();
        if ($auth->hasIdentity()) {
            $user = $auth->getIdentity();
        } else {
            return $this->redirect()->toRoute('signin', array('action' => 'index'));
        }
        if ($user->role == 'Operator') {
            return array('error' => 'You don\'t have permission to do this action');
        }
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('user', array('action' => 'add'));
        }
        try {
            $userEdit = $this->getUserTable()->getUser($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('user', array(
                        'action' => 'index'
            ));
        }
        if ($userEdit->role == 'System Admin' && $user->role != 'System Admin') {
            return array('error' => 'You don\'t have permission to do this action');
        }

        $form = new EditUserForm(null, $user->role);
        $form->bind($userEdit);
        $form->get('submit')->setAttribute('value', 'Edit');
        if ($userEdit->role == 'System Admin' && $userEdit->userId == 1) {
            $topRole = true;
            $form->get('role')->setAttribute('disabled', true);
            $form->getInputFilter()->get('role')->setRequired(false);
        }
        $form->getInputFilter()->get('password')->setRequired(false);
        $form->getInputFilter()->get('repassword')->setRequired(false);
        $form->getInputFilter()->get('activated')->setRequired(false);
        $form->getInputFilter()->get('activated')->setAllowEmpty(true);
        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setInputFilter($userEdit->getInputFilter());

            $form->setData($request->getPost());

            if ($form->isValid()) {
                if ($topRole) {
                    $userEdit->role = 'System Admin';
                }
                $this->getUserTable()->saveUser($userEdit);
                return $this->redirect()->toRoute('user');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
            'user' => $userEdit,
        );
    }

    public function changePasswordAction() {
        $auth = new \Zend\Authentication\AuthenticationService();
        if ($auth->hasIdentity()) {
            $user = $auth->getIdentity();
        } else {
            return $this->redirect()->toRoute('signin', array('action' => 'index'));
        }
        if ($user->role == 'Operator') {
            return array('error' => 'You don\'t have permission to do this action');
        }
        $id = (int) $this->params()->fromRoute('id', 0);

        try {
            $userEdit = $this->getUserTable()->getUser($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('user', array(
                        'action' => 'index'
            ));
        }
        if ($userEdit->role == 'System Admin' && $user->userId != $userEdit->userId) {
            return array('error' => 'You don\'t have permission to do this action');
        }
        $item = new ChangePasswordModel();
        $form = new ChangePasswordForm();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($item->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $item->userId = $userEdit->userId;
                $item->oldPassword = $form->get('oldPassword')->getValue();
                $item->newPassword = $form->get('newPassword')->getValue();
                $item->confirmNewPassword = $form->get('confirmNewPassword')->getValue();
//                try {
                $error = $this->getUserTable()->changePasswordForUser($item);
//                } catch (Exception $exc) {
//                    $error=$exc;
//                }
                if (!$error) {
                    return $this->redirect()->toRoute('user', array('action' => 'edit', 'id' => $id));
                }
            }
        }
        return array(
            'id' => $id,
            'form' => $form,
            'error' => $error
        );
    }

    public function detailAction() {
        $auth = new \Zend\Authentication\AuthenticationService();
        if ($auth->hasIdentity()) {
            $user = $auth->getIdentity();
        } else {
            return $this->redirect()->toRoute('signin', array('action' => 'index'));
        }


        $id = (int) $this->params()->fromRoute('id', 0);
        try {
            $userFind = $this->getUserTable()->getUser($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('user', array(
                        'action' => 'index'
            ));
        }

//        if (!$userFind) {
//            $error = 'This id doesn\'t exist';
//        }

        if ($userFind->role == 'System Admin' && $user->role != 'System Admin') {
            return new ViewModel(array('error' => 'You don\'t have permission to do this action'));
        }

        return new ViewModel(array(
            'id' => $id,
            'user' => $userFind,
            'error' => $error
        ));
    }

    public function deleteAction() {
        $auth = new \Zend\Authentication\AuthenticationService();
        if ($auth->hasIdentity()) {
            $user = $auth->getIdentity();
        } else {
            return $this->redirect()->toRoute('signin', array('action' => 'index'));
        }
        if ($user->role == 'Operator') {
            return array('error' => 'You don\'t have permission to do this action');
        }
        $id = (int) $this->params()->fromRoute('id', 0);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                if ($this->getUserTable()->getUser($id)->role == 'System Admin') {
                    return array('error' => 'You don\'t have permission to do this action');
                }
                $this->getUserTable()->deleteUser($id);
            }
            return $this->redirect()->toRoute('user');
        }

        return array(
            'id' => $id,
            'user' => $this->getUserTable()->getUser($id)
        );
    }

    public function getIndexLocation() {
        $config = $this->getServiceLocator()->get('config');
        if ($config instanceof Traversable) {
            $config = ArrayUtils::iteratorToArray($config);
        }
        if (!empty($config['module_config']['search_index'])) {
            return $config['module_config']['search_index'];
        } else {
            return FALSE;
        }
    }

    public function generateSearchAction() {
        $searchIndexLocation = $this->getIndexLocation();
        $index = Lucene\Lucene::create($searchIndexLocation);
        $allUsers = $this->getUserTable()->fetchAll(false);
        foreach ($allUsers as $user) {
            $id = Document\Field::keyword('userId', $user->userId);
            $firstName = Document\Field::text('firstName', $user->firstName);
            $lastName = Document\Field::text('lastName', $user->lastName);
            $email = Document\Field::text('email', $user->email);
            $role = Document\Field::text('role', $user->role);
            $activated = Document\Field::keyword('activated', $user->activated);
            $indexDoc = new Lucene\Document();
            $indexDoc->addField($id);
            $indexDoc->addField($firstName);
            $indexDoc->addField($lastName);
            $indexDoc->addField($email);
            $indexDoc->addField($role);
            $indexDoc->addField($activated);
            $index->addDocument($indexDoc);
        }
        $index->commit();
    }

    public function searchAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $queryText = $request->getPost()->get('query');
            $searchIndexLocation = $this->getIndexLocation();
            $index = Lucene\Lucene::open($searchIndexLocation);
            $searchResult = $index->find($queryText);
        }
        $form = new \Zend\Form\Form();
        $form->add(array(
            'name' => 'query',
            'attributes' => array(
                'type' => 'text',
                'id' => 'queryText',
            ),
            'options' => array(
                'label' => 'Search String',
            ),
        ));
        $form->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Search'
            ),
        ));
        $viewModel = new ViewModel(array(
            'form' => $form,
            'searchResults' => $searchResult
                )
        );
        return $viewModel;
    }
}
