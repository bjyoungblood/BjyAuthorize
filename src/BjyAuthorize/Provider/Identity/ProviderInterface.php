<?php

namespace BjyAuthorize\Provider\Identity;

interface ProviderInterface
{
    /**
     * @return array
     */
    public function getIdentityRoles();
    public function getDefaultRole();
    public function setDefaultRole($role);
}
