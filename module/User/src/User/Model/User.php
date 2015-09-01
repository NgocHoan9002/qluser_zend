<?php

namespace User\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator;

class User implements InputFilterAwareInterface {

    public $userId;
    public $firstName;
    public $lastName;
    public $email;
    public $password;
    //public $repassword;
    public $role;
    public $activated;
    protected $inputFilter;

    public function exchangeArray($data) {
        $this->userId = (isset($data['userId'])) ? $data['userId'] : null;
        $this->firstName = (isset($data['firstName'])) ? $data['firstName'] : null;
        $this->lastName = (isset($data['lastName'])) ? $data['lastName'] : null;
        $this->email = (isset($data['email'])) ? $data['email'] : null;
        $this->password = (isset($data['password'])) ? $data['password'] : null;
        //$this->repassword = (isset($data['repassword'])) ? $data['repassword'] : null;
        $this->role = (isset($data['role'])) ? $data['role'] : null;
        $this->activated = (isset($data['activated'])) ? $data['activated'] : null;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter) {
        throw new \Exception("Not used");
    }

    public function getInputFilter() {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name' => 'userId',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'firstName',
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
                            'max' => 30,
                        ),
                    ),
                    array(
                        'name' => 'Alpha',
                        'options' => array(
                            'allowWhiteSpace' => true,
                        )
                    )
                ),
            ));

            $inputFilter->add(array(
                'name' => 'lastName',
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
                            'max' => 30,
                        ),
                    ),
                    array(
                        'name' => 'Alpha',
                        'options' => array(
                            'allowWhiteSpace' => true,
                        )
                    )
                ),
            ));

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
                //'required' => true,
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
                'name' => 'repassword',
                //'required' => true,
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
                            'token' => 'password',
                        ),
                        'name' => 'Identical',
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'role',
                //'required' => false,
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
                            'max' => 30,
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'activated',
                'required' => false,
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}
