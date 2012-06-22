<?php

namespace BjyAuthorize\Provider\Role;

interface ProviderInterface
{
    /**
     * @return array of \Zend\Acl\Role\RoleInterface
     */
    public function getRoles();
}
