<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Signin\Form;

use Zend\Form\Form;

/**
 * Description of LoginForm
 *
 * @author loc
 */
class SigninForm extends Form {
    //put your code here
    public function __construct($name = null) {
        parent::__construct('signin');
        
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'email',
            'attributes' => array(
                'type' => 'text',
            ),
            'options' => array(
                'label' => 'Email',
            ),
        ));
        $this->add(array(
            'name' => 'password',
            'attributes' => array(
                'type' => 'password',
            ),
            'options' => array(
                'label' => 'Password',
            ),
        ));
        $this->add(array(
            'name' => 'rememberme',
            'attributes' => array(
                'type' => 'checkbox',
            ),
            'options' => array(
                'label' => 'Remember me?'
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Login',
                'id' => 'submitbutton',
            ),
        ));
    }
}
