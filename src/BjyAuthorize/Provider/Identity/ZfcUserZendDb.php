<?php

namespace BjyAuthorize\Provider\Identity;

use BjyAuthorize\Acl\Role;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Sql;

class ZfcUserZendDb implements ProviderInterface
{
    protected $userService;
    protected $defaultRole;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function getIdentity()
    {
        $authService = $this->userService->getAuthService();

        $sql = new Sql($this->adapter);
        $select = $sql->select();

        if (!$authService->hasIdentity()) {
            // get default/guest role
            return $this->getDefaultRole();
        } else {
            // get roles associated with the logged in user
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
