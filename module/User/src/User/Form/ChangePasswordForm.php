<?php
namespace User\Form;

use Zend\Form\Form;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ChangePasswordForm extends Form {
    public function __construct($name = null) {
        parent::__construct('user');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'userId',
            'attributes' => array(
                'type' => 'hidden',
            ),
        ));
        $this->add(array(
            'name' => 'oldPassword',
            'attributes' => array(
                'type' => 'password',
            ),
            'options' => array(
                'label' => 'Enter your old password',
            ),
            
        ));
        
        $this->add(array(
            'name' => 'newPassword',
            'attributes' => array(
                'type' => 'password',
            ),
            'options' => array(
                'label' => 'Enter your new password',
            ),
        ));
        $this->add(array(
            'name' => 'confirmNewPassword',
            'attributes' => array(
                'type' => 'password',
            ),
            'options' => array(
                'label' => 'Confirm your new password',
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Save changes',
                'id' => 'submitbutton',
            ),
        ));
    }
}

