<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Provider\Identity;

use BjyAuthorize\Exception\InvalidRoleException;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Permissions\Acl\Role\RoleInterface;
use ZfcUser\Service\User;

/**
 * Identity provider based on {@see \Zend\Db\Adapter\Adapter}
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 */
class ZfcUserZendDb implements ProviderInterface
{
    /**
     * @var User
     */
    protected $userService;

    /**
     * @var string|\Zend\Permissions\Acl\Role\RoleInterface
     */
    protected $defaultRole;

    /**
     * @var \Zend\Db\TableGateway\TableGateway
     */
    private $tableGateway;

    /**
     * @param \Zend\Db\TableGateway\TableGateway $tableGateway
     * @param \ZfcUser\Service\User              $userService
     */
    public function __construct(TableGateway $tableGateway, User $userService)
    {
        $this->tableGateway = $tableGateway;
        $this->userService  = $userService;
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentityRoles()
    {
        $authService = $this->userService->getAuthService();

        if (! $authService->hasIdentity()) {
            return array($this->getDefaultRole());
        }

        $roleProvider = $this->getZendDbRoleProvider();

        // get roles associated with the logged in user
        $sql = new Select();
        $sql->from($this->tableGateway->getTable());
        $sql->join($roleProvider->getTableName(), $roleProvider->getTableName() . '.' .
            $roleProvider->getIdentifierFieldName() . ' = ' . $this->tableGateway->getTable() . '.' .
            $roleProvider->getRoleIdFieldName()
        );
        $sql->where(array('user_id' => $authService->getIdentity()->getId()));

        $results = $this->tableGateway->selectWith($sql);

        $roles = array();

        foreach ($results as $role) {
            $roles[] = $role['role_id'];
        }

        return $roles;
    }

    /**
     * @return \BjyAuthorize\Provider\Role\ZendDb
     */
    private function getZendDbRoleProvider()
    {
        return $this->userService->getServiceManager()->get('BjyAuthorize\Provider\Role\ZendDb');
    }

    /**
     * @return string|\Zend\Permissions\Acl\Role\RoleInterface
     */
    public function getDefaultRole()
    {
        return $this->defaultRole;
    }

    /**
     * @param string|\Zend\Permissions\Acl\Role\RoleInterface $defaultRole
     *
     * @throws \BjyAuthorize\Exception\InvalidRoleException
     */
    public function setDefaultRole($defaultRole)
    {
        if (! ($defaultRole instanceof RoleInterface || is_string($defaultRole))) {
            throw InvalidRoleException::invalidRoleInstance($defaultRole);
        }

        $this->defaultRole = $defaultRole;
    }
}
