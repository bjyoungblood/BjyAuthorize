<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Provider\Identity;

use BjyAuthorize\Exception\InvalidRoleException;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Sql;
use Zend\Permissions\Acl\Role\RoleInterface;
use ZfcUser\Service\User;
use ZfcUser\Entity\UserInterface;

/**
 * Identity provider based on {@see \Zend\Db\Adapter\Adapter}
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 */
class ZfcUserZendDb implements ProviderInterface
{

    const DEFAULT_TABLE_NAME = 'user_role_linker';

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
    protected $tableName;

    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     * @param \ZfcUser\Service\User    $userService
     */
    public function __construct(Adapter $adapter, User $userService)
    {
        $this->adapter     = $adapter;
        $this->userService = $userService;
    }

    /**
     * Retrieve roles for the current identity
     *
     * @return string[]|\Zend\Permissions\Acl\Role\RoleInterface[]
     */
    public function getIdentityRoles($userId = null)
    {
        $authService = $this->userService->getAuthService();

        if ($userId === null) {
            if ( ! $authService->hasIdentity()) {
                return array($this->getDefaultRole());
            }

            $userId = $authService->getIdentity()->getId();
        } elseif ($userId instanceof UserInterface) {
            $userId = $userId->getId();
        }

        // get roles associated with the logged in user
        $sql    = new Sql($this->adapter);
        $select = $sql->select()->from($this->getTableName());
        $where  = new Where();

        $where->equalTo('user_id', $userId);

        $results = $sql->prepareStatementForSqlObject($select->where($where))->execute();
        $roles   = array();

        foreach ($results as $i) {
            $roles[] = $i['role_id'];
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
        if ( ! ($defaultRole instanceof RoleInterface || is_string($defaultRole))) {
            throw InvalidRoleException::invalidRoleInstance($defaultRole);
        }

        $this->defaultRole = $defaultRole;
    }

    /**
     * Set table name
     *
     * @param string $name of the table to use
     * @return \BjyAuthorize\Provider\Identity\ZfcUserZendDb
     */
    public function setTableName($name)
    {
        $this->tableName = (string) $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        if (!$this->tableName) {
            $this->setTableName(static::DEFAULT_TABLE_NAME);
        }

        return $this->tableName;
    }
}
