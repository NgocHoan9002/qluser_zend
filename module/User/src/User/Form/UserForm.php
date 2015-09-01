<?php

namespace User\Form;

use Zend\Form\Form;

//use Zend\Validator\Identical;

class UserForm extends Form {

    public function __construct($name = null,$role = null) {
        parent::__construct('user');
        
        $this->add(array(
            'name' => 'userId',
            'type' => 'Hidden',
        ));

        $this->add(array(
            'name' => 'firstName',
            'type' => 'text',
            'options' => array(
                'label' => 'First name:',
            ),
            'required' => true,
        ));

        $this->add(array(
            'name' => 'lastName',
            'type' => 'text',
            'options' => array(
                'label' => 'Last name:',
            ),
            'required' => true,
        ));

        $this->add(array(
            'name' => 'email',
            'type' => 'Zend\Form\Element\Email',
            'options' => array(
                'label' => 'Email address:',
            ),
            'required' => true,
        ));

        $this->add(array(
            'name' => 'password',
            'type' => 'Zend\Form\Element\Password',
            'options' => array(
                'label' => 'Password:',
            ),
            'required' => true,
        ));

        $this->add(array(
            'name' => 'repassword',
            'type' => 'Zend\Form\Element\Password',
            'options' => array(
                'label' => 'Confirm your password:',
            ),
            'required' => true,
        ));
        
        if ($role == 'System Admin') {
            $myarray = array(
                    'System Admin' => 'System Admin',
                    'Super Admin' => 'Super Admin',
                    'Operator' => 'Operator',
            );
            
        } else {
            $myarray = array(
                    'Super Admin' => 'Super Admin',
                    'Operator' => 'Operator',
            );
        }

        $this->add(array(
            'name' => 'role',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'role',
                'options' => $myarray
            ),
            'options' => array(
                'label' => 'Role:',
            ),
            'required' => true,
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'activated',
            'options' => array(
                'label' => 'Activated: ',
                'use_hidden_element' => true,
                'checked_value' => 1,
                'unchecked_value' => 0
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'id' => 'btnAdd',
            ),
        ));
    }

}
