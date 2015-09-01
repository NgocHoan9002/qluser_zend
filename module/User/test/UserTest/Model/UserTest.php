<?php

namespace UserTest\Model;

use User\Model\User;
use PHPUnit_Framework_TestCase;

class UserTest extends PHPUnit_Framework_TestCase {

    public function testUserInitialState() {
        $user = new User();

        $this->assertNull(
                $user->userId, '"userId" should initially be null'
        );
        $this->assertNull(
                $user->firstName, '"firstName" should initially be null'
        );
        $this->assertNull(
                $user->lastName, '"lastName" should initially be null'
        );
        $this->assertNull(
                $user->email, '"email" should initially be null'
        );
        $this->assertNull(
                $user->password, '"password" should initially be null'
        );
        $this->assertNull(
                $user->role, '"role" should initially be null'
        );
        $this->assertNull(
                $user->activated, '"activated" should initially be null'
        );
    }

    public function testExchangeArraySetsPropertiesCorrectly() {
        $user = new User();
        $data = array('userId' => 123,
            'firstName' => 'Ho',
            'lastName' => 'Kiet',
            'email' => 'kiet.ho@mail.com',
            'password' => '123',
            'role' => 'Operator',
            'activated' => 1);

        $user->exchangeArray($data);

        $this->assertSame(
                $data['userId'], $user->userId, '"userId" was not set correctly'
        );
        $this->assertSame(
                $data['firstName'], $user->firstName, '"firstName" was not set correctly'
        );
        $this->assertSame(
                $data['lastName'], $user->lastName, '"lastName" was not set correctly'
        );
        $this->assertSame(
                $data['email'], $user->email, '"email" was not set correctly'
        );
        $this->assertSame(
                $data['password'], $user->password, '"password" was not set correctly'
        );
        $this->assertSame(
                $data['role'], $user->role, '"role" was not set correctly'
        );
        $this->assertSame(
                $data['activated'], $user->activated, '"lastName" was not set correctly'
        );
    }

    public function testExchangeArraySetsPropertiesToNullIfKeysAreNotPresent() {
        $user = new User();

        $data = array('userId' => 123,
            'firstName' => 'Ho',
            'lastName' => 'Kiet',
            'email' => 'kiet.ho@mail.com',
            'password' => '123',
            'role' => 'Operator',
            'activated' => 1);

        $user->exchangeArray($data);
        $user->exchangeArray(array());

        $this->assertNull(
                $user->userId, '"userId" should have defaulted to null'
        );
        $this->assertNull(
                $user->firstName, '"firstName" should have defaulted to null'
        );
        $this->assertNull(
                $user->lastName, '"lastName" should have defaulted to null'
        );
        $this->assertNull(
                $user->email, '"email" should have defaulted to null'
        );
        $this->assertNull(
                $user->password, '"password" should have defaulted to null'
        );
        $this->assertNull(
                $user->role, '"role" should have defaulted to null'
        );
        $this->assertNull(
                $user->activated, '"activated" should have defaulted to null'
        );
    }

    public function testGetArrayCopyReturnsAnArrayWithPropertyValues() {
        $user = new User();
        $data = array('userId' => 123,
            'firstName' => 'Ho',
            'lastName' => 'Kiet',
            'email' => 'kiet.ho@mail.com',
            'password' => '123',
            'role' => 'Operator',
            'activated' => 1);

        $user->exchangeArray($data);
        $copyArray = $user->getArrayCopy();

        $this->assertSame(
                $data['userId'], $copyArray['userId'], '"userId" was not set correctly'
        );
        $this->assertSame(
                $data['firstName'], $copyArray['firstName'], '"firstName" was not set correctly'
        );
        $this->assertSame(
                $data['lastName'], $copyArray['lastName'], '"lastName" was not set correctly'
        );
        $this->assertSame(
                $data['email'], $copyArray['email'], '"email" was not set correctly'
        );
        $this->assertSame(
                $data['password'], $copyArray['password'], '"password" was not set correctly'
        );
        $this->assertSame(
                $data['role'], $copyArray['role'], '"role" was not set correctly'
        );
        $this->assertSame(
                $data['activated'], $copyArray['activated'], '"activated" was not set correctly'
        );
    }

    public function testInputFiltersAreSetCorrectly()
    {
        $user = new User();

        $inputFilter = $user->getInputFilter();

        $this->assertSame(8, $inputFilter->count());
        $this->assertTrue($inputFilter->has('userId'));
        $this->assertTrue($inputFilter->has('firstName'));
        $this->assertTrue($inputFilter->has('lastName'));
    }
}
