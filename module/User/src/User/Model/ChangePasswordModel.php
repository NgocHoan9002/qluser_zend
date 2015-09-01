<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace User\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class ChangePasswordModel implements InputFilterAwareInterface {
    public $userId;
    public $oldPassword;
    public $newPassword;
    public $confirmNewPassword;
    
    protected $inputFilter;
    
    public function getInputFilter() {
        if (!$this->inputFilter) {
            $inputFilter=new InputFilter();
            
            $inputFilter->add(array(
                'name' => 'userId',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            
            $inputFilter->add(array(
                'name' => 'oldPassword',
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
                'name' => 'newPassword',
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
                'name' => 'confirmNewPassword',
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
                            'token' => 'newPassword',
                        ),
                        'name' => 'Identical',
                    ),
                ),
            ));
            
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

    public function setInputFilter(InputFilterInterface $inputFilter) {
        throw new \Exception('Not used');
    }

}

