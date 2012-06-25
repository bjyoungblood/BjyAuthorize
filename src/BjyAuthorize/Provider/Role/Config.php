<?php

namespace BjyAuthorize\Provider\Role;

use BjyAuthorize\Acl\Role;

class Config implements ProviderInterface
{
    protected $roles = array();

    public function __construct(array $config = array())
    {
        $roles = array();
        foreach ($config as $key => $value) {
            if (is_numeric($key)) {
                $roles = array_merge($roles, $this->loadRole($value));
            } else {
                $roles = array_merge($roles, $this->loadRole($key, $value));
            }
        }

        $this->roles = $roles;
    }

    public function loadRole($name, $options = array(), $parent = null)
    {
        if (isset($options['children']) && count($options['children']) > 0) {
            $children = $options['children'];
        } else {
            $children = array();
        }

        $roles = array();
        $role = new Role($name, $parent);
        $roles[] = $role;

        foreach ($children as $key => $value) {
            if (is_numeric($key)) {
                $roles = array_merge($roles, $this->loadRole($value, array(), $role));
            } else {
                $roles = array_merge($roles, $this->loadRole($key, $value, $role));
            }
        }

        return $roles;
    }

    public function getRoles()
    {
        return $this->roles;
    }
}
