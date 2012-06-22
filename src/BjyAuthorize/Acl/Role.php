<?php

namespace BjyAuthorize\Acl;

use Zend\Acl\Role\RoleInterface;

class Role implements RoleInterface
{
    protected $roleId;
    protected $default;
    protected $parent;

    public function __construct($roleId = null, $default = false, RoleInterface $parent = null)
    {
        $this->roleId   = $roleId;
        $this->default  = $default;
        $this->parent   = $parent;
    }
 
    public function getRoleId()
    {
        return $this->roleId;
    }
 
    public function setRoleId($roleId)
    {
        $this->roleId = $roleId;
        return $this;
    }
 
    public function getDefault()
    {
        return $this->default;
    }
 
    public function setDefault($default)
    {
        $this->default = $default;
        return $this;
    }
 
    public function getParent()
    {
        return $this->parent;
    }
 
    public function setParent($parent)
    {
        $this->parent = $parent;
        return $this;
    }
}
