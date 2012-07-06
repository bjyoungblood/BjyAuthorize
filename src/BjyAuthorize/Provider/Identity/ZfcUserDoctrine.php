<?php

namespace BjyAuthorize\Provider\Identity;

use BjyAuthorize\Acl\Role;
use Doctrine\ORM\EntityManager;

class ZfcUserDoctrine implements ProviderInterface
{
    protected $userService;
    protected $defaultRole;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getIdentityRoles()
    {
        $authService = $this->userService->getAuthService();

        if (!$authService->hasIdentity()) {
            // get default/guest role
            return $this->getDefaultRole();
        } else {
            // get roles associated with the logged in user
            $builder = new \Doctrine\DBAL\Query\QueryBuilder($this->em->getConnection());
            $builder->select("role.role_id")
                ->from('user_role_linker', 'user_role_linker')
                ->where('role.user_id = ?', $authService->getIdentity()->getId());

            $result = $builder->execute();

            $roles = array();
            foreach($result as $row) {
                $roles[] = $row['role_id'];
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
