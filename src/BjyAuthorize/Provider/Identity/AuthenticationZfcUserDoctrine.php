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
use Zend\Permissions\Acl\Role\RoleInterface;
use Zend\Authentication\AuthenticationService;

/**
 * Identity provider based on {@see \Doctrine\ORM\EntityManager}
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 *
 * @deprecated you should use {@see \BjyAuthorize\Provider\Identity\AuthenticationDoctrineEntity} instead
 */
class AuthenticationZfcUserDoctrine extends ZfcUserDoctrine implements ProviderInterface {

    /**
     * @var string|\Zend\Permissions\Acl\Role\RoleInterface
     */
    protected $defaultRole;

    /**
     * @var string|\Zend\Permissions\Acl\Role\RoleInterface
     */
    protected $authenticatedRoles = array();

    /**
     * Get the rule that's used if you're authenticated
     *
     * @return string
     */
    public function getAuthenticatedRoles() {
        return $this->authenticatedRoles;
    }

    /**
     * Set the role that's used if you're authenticated
     *
     * @param string $authenticatedRole
     */
    public function setAuthenticatedRole($authenticatedRole) {
        $this->authenticatedRoles[] = $authenticatedRole;
    }

}