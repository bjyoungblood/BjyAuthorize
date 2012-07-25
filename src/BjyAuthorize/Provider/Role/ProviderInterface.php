<?php

namespace BjyAuthorize\Provider\Role;

interface ProviderInterface
{
    /**
     * @return array of \Zend\Permissions\Acl\Role\RoleInterface
     */
    public function getRoles();
}
