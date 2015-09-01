<?php

namespace UserTest\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;

class UserControllerTest extends AbstractHttpControllerTestCase {

    protected $traceError = true;
    private $auth;

    public function setUp() {
        $this->setApplicationConfig(
                include '/config/application.config.php'
        );
        parent::setUp();
    }

    public function awakeSignIn() {
        $this->auth = new AuthenticationService();

        $sm = $this->getApplicationServiceLocator();
        $sm->setAllowOverride(true);
        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

        $authAdapter = new AuthAdapter($dbAdapter, 'user', 'email', 'password');
        $authAdapter->setIdentity('kiet@mail.com');
        $authAdapter->setCredential(md5('123'));
        $this->auth->authenticate($authAdapter);
        $this->assertTrue($this->auth->hasIdentity());
    }

    public function awakeSignOut() {
        $this->auth->clearIdentity();
    }

    public function testIndexActionCanBeAccessed() {
        $this->awakeSignIn();
        $this->dispatch('/signin/signin');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/user');
        $this->awakeSignOut();
    }

    public function testIndexActionWithoutLogin() {
        $this->dispatch('/user');
        $this->assertResponseStatusCode(302);
        $this->assertRedirect('/signin/signin');
    }

    public function testDeleteActionRedirectsAfterValidPost() {
        $postData = array(
            'email' => 'kiet@mail.com',
            'password' => '123',
        );
        $this->dispatch('/signin/signin', 'POST', $postData);
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/user');
        
        $userTableMock = $this->getMockBuilder('User\Model\UserTable')
                ->disableOriginalConstructor()
                ->getMock();

        $userTableMock->expects($this->any())
                ->method('deleteUser')
                ->will($this->returnValue(null));

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('User\Model\UserTable', $userTableMock);

        $postData = array(
            'id' => 12,
            'del' => 'Yes'
        );
        $this->dispatch('/user/delete/12', 'POST', $postData);
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/user');
    }

    public function testAddActionLogoutAlreadyRedirectToSignIn() {
        $this->awakeSignIn();
        $this->awakeSignOut();
        $this->dispatch('/user/add');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/signin');
    }

//    public function testAddActionWithOperatorRole() {
//        $postData = array(
//            'email' => 'kiet@mail.com',
//            'password' => '123',
//            'submit' => 'Login'
//        );
//        $this->dispatch('/signin/signin', 'POST', $postData);
//        $this->assertResponseStatusCode(302);
//        $this->assertRedirectTo('/user');
//
//        $this->dispatch('/user/add');
//        $this->assertEquals('You don', $this->returnArgument('error'));
        //$this->assertResponseStatusCode(200);


//        $this->assertEquals('aaa', getResponse());
        //$this->assertNull($var);
//        $this->awakeSignIn();
//        $postData = array(
//            'firstName' => 'robin',
//            'lastName' => 'son',
//            'email' => 'robinson@mail.com',
//            'password' => '123',
//            'role' => 'Operator',
//            'activated' => 1,
//            'userId' => '',
//        );
//        
//        $var1 = $this->dispatch('/user/add');
//        $this->assertArrayHasKey('error', $var1);
//        $this->assertNull($var['error']);
        //$this->assertResponseStatusCode(302);
        //$this->assertEqual('You don\'t have permission to do this action', $val->error);
//    }


    public function testAddActionRedirectsAfterValidPost() {
        $postData = array(
            'email' => 'kiet@mail.com',
            'password' => '123',
            'submit' => 'Login'
        );
        $this->dispatch('/signin/signin', 'POST', $postData);
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/user');
        
        $userTableMock = $this->getMockBuilder('User\Model\UserTable')
                ->disableOriginalConstructor()
                ->getMock();

        $userTableMock->expects($this->any())
                ->method('saveUser')
                ->will($this->returnValue(null));

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('User\Model\UserTable', $userTableMock);

        $postData = array(
            'firstName' => 'Huyq',
            'lastName' => 'Kietq',
            'email' => 'huykietq@mail.com',
            'password' => '123q',
            'repassword' => '123q',
            'role' => 'Super Admin',
            'submit' => 'Add',
            'activated' => 0,
            'userId' => ''
        );
        $this->dispatch('/user/add', 'POST', $postData);
        $this->assertResponseStatusCode(302);
        $this->assertActionName('add');
        $this->assertModuleName('user');
        $this->assertRedirectTo('/user');
    }

    public function testEditActionRedirectsAfterValidPost() {
        $postData = array(
            'email' => 'kiet@mail.com',
            'password' => '123',
            'submit' => 'Login'
        );
        $this->dispatch('/signin/signin', 'POST', $postData);
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/user');
        
        $userTableMock = $this->getMockBuilder('User\Model\UserTable')
                ->disableOriginalConstructor()
                ->getMock();

        $userTableMock->expects($this->any())
                ->method('saveUser')
                ->will($this->returnValue(null));

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('User\Model\UserTable', $userTableMock);

        $postData = array(
            'userId' => 17,
            'firstName' => 'DaylaTest',
            'lastName' => 'daylatest',
            'email' => 'daylatest@decongen.kobuonngu',
            'role' => 'Super Admin',
            'activated' => 0,
            'activated' => 1,
            'submit' => 'Edit',
        );
        $this->dispatch('/user/edit/17', 'POST', $postData);
        $this->assertActionName('edit');
        $this->assertModuleName('user');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/user');
    }
}
