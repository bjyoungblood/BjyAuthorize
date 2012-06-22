<?php

namespace BjyAuthorize\Provider\Identity;

interface ProviderInterface
{
    /**
     * @return Zend\Acl\Role\RoleInterface
     */
    public function getIdentity();
    public function getDefaultRole();
    public function setDefaultRole($role);
}
