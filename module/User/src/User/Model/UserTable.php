<?php

namespace User\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class UserTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll($paginated = false) {
        if ($paginated) {
            $select = new Select('user');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new User());
            $paginatorAdapter = new DbSelect(
                $select, $this->tableGateway->getAdapter(), $resultSetPrototype
            );
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getUser($id) {
        $id2 = (int) $id;
        $rowset = $this->tableGateway->select(array('userId' => $id2,));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id2");
        }
        return $row;
    }

    public function saveUser(User $user) {
        $data = array(
            'firstName' => $user->firstName,
            'lastName' => $user->lastName,
            'email' => $user->email,
            'password' => md5($user->password),
            'role' => $user->role,
            'activated' => $user->activated,
        );

        $id = (int) $user->userId;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            $userEdit=$this->getUser($id);
            if ($userEdit) {
                $data = array(
                    'userId' => $user->userId,
                    'firstName' => $user->firstName,
                    'lastName' => $user->lastName,
                    'email' => $user->email,
                    'password' => $userEdit->password,
                    'role' => $user->role,
                    'activated' => $user->activated,
                );
                $this->tableGateway->update($data, array('userId' => $id));
            } else {
                throw new \Exception('User id does not exist');
            }
        }
    }
    
    public function changePasswordForUser(ChangePasswordModel $user) {
        $data = array(
            'password' => md5($user->newPassword),
        );
        $id = (int) $user->userId;
        $userEdit=$this->getUser($id);
        if ($userEdit) {
            if (md5($user->oldPassword)!=$userEdit->password) {
                $error='You have entered a wrong old password';
            } else{
                $this->tableGateway->update($data, array('userId' => $id));
                $error='';
            }
        } else {
            $error='User id does not exist';
        }
        return $error;
    }

    public function deleteUser($id) {
        $this->tableGateway->delete(array('userId' => (int) $id));
    }

}
