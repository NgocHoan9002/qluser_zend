<?php

namespace Signin\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Signin\Model\SigninUserModel;
use User\Model\User;

class SigninTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    
    /**
     * 
     * @param SigninUserModel $user
     * @return boolean
     */
    public function getRole(SigninUserModel $user) {
        $data = array(
            'email' => $user->email,
            'password' => md5($user->password)
        );
        $rowset = $this->tableGateway->select($data);
        $row = $rowset->current();
        if ($row) {
            return $row->role;
        }
        return NULL;
    }

}
