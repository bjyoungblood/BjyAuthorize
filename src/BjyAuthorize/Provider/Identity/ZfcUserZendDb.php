<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Provider\Identity;

use BjyAuthorize\Exception\InvalidRoleException;
use BjyAuthorize\Provider\Role\ZendDb;
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
     * @var \BjyAuthorize\Provider\Role\ZendDb
     */
    private $zendDbRole;

    /**
     * @param \Zend\Db\TableGateway\TableGateway $tableGateway
     * @param \ZfcUser\Service\User $userService
     * @param \BjyAuthorize\Provider\Role\ZendDb $zendDb
     */
    public function __construct(TableGateway $tableGateway, User $userService, ZendDb $zendDbRole)
    {
        $this->tableGateway = $tableGateway;
        $this->userService  = $userService;
        $this->zendDbRole   = $zendDbRole;
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

        // get roles associated with the logged in user
        $sql = new Select();
        $sql->from($this->tableGateway->getTable());
        $sql->join($this->zendDbRole->getTableName(), $this->zendDbRole->getTableName() . '.' .
            $this->zendDbRole->getIdentifierFieldName() . ' = ' . $this->tableGateway->getTable() . '.role_id');
        $sql->where(array('user_id' => $authService->getIdentity()->getId()));

        $results = $this->tableGateway->selectWith($sql);

        $roles = array();

        foreach ($results as $role) {
            $roles[] = $role['role_id'];
        }

        return $roles;
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
