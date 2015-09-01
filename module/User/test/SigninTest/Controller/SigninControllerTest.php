<?php

namespace SigninTest\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;

class SigninControllerTest extends AbstractHttpControllerTestCase {

    protected $traceError = true;

    public function setUp() {
        $this->setApplicationConfig(
                include '/config/application.config.php'
        );
        parent::setUp();
    }

    private $result;

    public function testSigninAction() {
        $auth = new AuthenticationService();
        $sm = $this->getApplicationServiceLocator();
        $sm->setAllowOverride(true);
        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

        $authAdapter = new AuthAdapter($dbAdapter, 'user', 'email', 'password');
        $authAdapter->setIdentity('sysad@gmail.com');
        $authAdapter->setCredential(md5('sysad'));
        $this->result = $auth->authenticate($authAdapter);
        $this->assertTrue($auth->hasIdentity());
    }

    public function testSigninActionAnotherWay() {
        $postData = array(
            'email' => 'kiet@mail.com',
            'password' => '123',
            'submit' => 'Login'
        );
        $this->dispatch('/signin/signin', 'POST', $postData);
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/user');
    }

    public function testLogoutAction() {
        $postData = array(
            'email' => 'kiet@mail.com',
            'password' => '123',
            'submit' => 'Login'
        );
        $this->dispatch('/signin/signin', 'POST', $postData);
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/user');

        $this->dispatch('/signin/logout');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/user');
    }

    public function testSigninWithWrongCredentials() {
        $auth = new AuthenticationService();
        $sm = $this->getApplicationServiceLocator();
        $sm->setAllowOverride(true);
        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
        $messages = null;
        $authAdapter = new AuthAdapter($dbAdapter, 'user', 'email', 'password');
        $authAdapter->setIdentity('kiet@mail.com');
        $authAdapter->setCredential(md5('456'));
        $this->result = $auth->authenticate($authAdapter);
        foreach ($this->result->getMessages() as $message) {
            $messages.="$message";
        }
        $this->assertEquals($messages, 'Supplied credential is invalid.');
    }

}
