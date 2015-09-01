<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Signin\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * Description of LoginUserModel
 *
 * @author loc
 */
class SigninUserModel implements InputFilterAwareInterface {
    
    public $email;
    public $password;
    protected $inputFilter;
    
    public function getInputFilter() {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $inputFilter->add(array(
                'name' => 'email',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                                    'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 50,
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'password',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 50,
                        ),
                    ),
                ),
            ));
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

    public function setInputFilter(InputFilterInterface $inputFilter) {
        throw new Exception('Not used');
    }

//put your code here
}
