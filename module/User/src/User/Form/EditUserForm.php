<?php

namespace User\Form;

use Zend\Form\Form;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class EditUserForm extends Form {

    public function __construct($name = null, $role = null) {
        // we want to ignore the name passed
        parent::__construct('user');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'userId',
            'attributes' => array(
                'type' => 'hidden',
            ),
        ));
        $this->add(array(
            'name' => 'firstName',
            'attributes' => array(
                'type' => 'text',
            ),
            'options' => array(
                'label' => 'First name',
            ),
        ));
        $this->add(array(
            'name' => 'lastName',
            'attributes' => array(
                'type' => 'text',
            ),
            'options' => array(
                'label' => 'Last name',
            ),
        ));

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
            'name' => 'repassword',
            'attributes' => array(
                'type' => 'password',
            ),
            'options' => array(
                'label' => 'Confirm Password',
            ),
        ));

        if ($role == 'System Admin') {
            $myoption = array(
                'System Admin' => 'System Admin',
                'Super Admin' => 'Super Admin',
                'Operator' => 'Operator',
            );
        } else {
            $myoption = array(
                'Super Admin' => 'Super Admin',
                'Operator' => 'Operator',
            );
        }

        $this->add(array(
            'name' => 'role',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Role',
                'value_options' => $myoption
        )));
        $this->add(array(
            'name' => 'activated',
            'type' => 'Zend\Form\Element\Checkbox',
            'options' => array(
                'label' => 'Activated',
            ),
            'attributes' => array(
                'value' => 'No'
            )
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
