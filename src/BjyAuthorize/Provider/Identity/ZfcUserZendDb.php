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

        $config = $this->userService->getServiceManager()->get('BjyAuthorize\Config');
        $tableName = $this->getUserRoleTableName($config);
        $identifierFieldName = $this->getUserRoleIdentifierFieldName($config);

        // get roles associated with the logged in user
        $sql = new Select();
        $sql->from($this->tableGateway->getTable());
        $sql->join($tableName, $tableName . '.' . $identifierFieldName . ' = ' . $this->tableGateway->getTable() . '.role_id');
        $sql->where(array('user_id' => $authService->getIdentity()->getId()));

        $results = $this->tableGateway->selectWith($sql);

        $roles = array();

        foreach ($results as $role) {
            $roles[] = $role['role_id'];
        }

        return $roles;
    }

    /**
     * @param $config
     * @return string
     */
    private function getUserRoleTableName($config)
    {
        $tableName = 'user_role';
        if (isset($config['role_providers']['BjyAuthorize\Provider\Role\ZendDb']) &&
            isset($config['role_providers']['BjyAuthorize\Provider\Role\ZendDb']['table'])) {
            $tableName = $config['role_providers']['BjyAuthorize\Provider\Role\ZendDb']['table'];
        }

        return $tableName;
    }

    /**
     * @param $config
     * @return string
     */
    private function getUserRoleIdentifierFieldName($config)
    {
        $identifierFieldName = 'id';
        if (isset($config['role_providers']['BjyAuthorize\Provider\Role\ZendDb']) &&
            isset($config['role_providers']['BjyAuthorize\Provider\Role\ZendDb']['identifier_field_name'])) {
            $identifierFieldName = $config['role_providers']['BjyAuthorize\Provider\Role\ZendDb']['identifier_field_name'];
        }

        return $identifierFieldName;
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
