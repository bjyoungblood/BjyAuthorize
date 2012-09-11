<?php

namespace BjyAuthorize\Provider\Identity;

use BjyAuthorize\Acl\Role;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Sql;

class ZfcUserZendDb implements ProviderInterface
{
    protected $userService;
    protected $defaultRole;

    protected $tableName = 'user_role_linker';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function getIdentityRoles()
    {
        $authService = $this->userService->getAuthService();

        if (!$authService->hasIdentity()) {
            // get default/guest role
            return $this->getDefaultRole();
        } else {
            // get roles associated with the logged in user
            $sql = new Sql($this->adapter);
            $select = $sql->select()
                ->from($this->tableName);

            $where = new Where();
            $where->equalTo('user_id', $authService->getIdentity()->getId());

            $statement = $sql->prepareStatementForSqlObject($select->where($where));

            $results = $statement->execute();

            $roles = array();
            foreach ($results as $i) {
                $roles[] = $i['role_id'];
            }

            return $roles;
        }
    }

    public function getUserService()
    {
        return $this->userService;
    }

    public function setUserService($userService)
    {
        $this->userService = $userService;
        return $this;
    }

    public function getDefaultRole()
    {
        return $this->defaultRole;
    }

    public function setDefaultRole($defaultRole)
    {
        $this->defaultRole = $defaultRole;
        return $this;
    }
}
