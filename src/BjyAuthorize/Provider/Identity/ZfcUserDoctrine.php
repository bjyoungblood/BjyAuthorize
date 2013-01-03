<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Provider\Identity;

use Doctrine\ORM\EntityManager;
use ZfcUser\Service\User;

/**
 * Identity provider based on {@see \Doctrine\ORM\EntityManager}
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 */
class ZfcUserDoctrine implements ProviderInterface
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var User
     */
    protected $userService;

    /**
     * @var string|\Zend\Permissions\Acl\Role\RoleInterface
     */
    protected $defaultRole;

    /**
     * @var string
     */
    protected $tableName = 'user_role_linker';

    /**
     * @param EntityManager $entityManager
     * @param User          $userService
     */
    public function __construct(EntityManager $entityManager, User $userService)
    {
        $this->entityManager = $entityManager;
        $this->userService   = $userService;
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentityRoles()
    {
        $authService = $this->userService->getAuthService();

        if (!$authService->hasIdentity()) {
            // get default/guest role
            return $this->getDefaultRole();
        } else {
            // get roles associated with the logged in user
            $builder = $this->entityManager->getConnection()->createQueryBuilder();
            $builder->select("linker.role_id")
                ->from($this->tableName, 'linker')
                ->where('linker.user_id = :user_id')
                ->setParameter('user_id', $authService->getIdentity()->getId());
            $result = $builder->execute();

            $roles = array();

            foreach ($result as $row) {
                $roles[] = $row['role_id'];
            }

            return $roles;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultRole()
    {
        return $this->defaultRole;
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultRole($defaultRole)
    {
        $this->defaultRole = $defaultRole;
    }
}
