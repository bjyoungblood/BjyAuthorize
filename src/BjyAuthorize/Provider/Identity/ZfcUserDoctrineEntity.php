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
 * Identity provider which uses doctrine User and Role entities.
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 * @author Tom Oram <tom@scl.co.uk>
 */
class ZfcUserDoctrineEntity extends ZfcUserDoctrine
{
    /**
     * {@inheritDoc}
     */
    public function getIdentityRoles()
    {
        $authService = $this->userService->getAuthService();

        if (!$authService->hasIdentity()) {
            // get default/guest role
            return $this->getDefaultRole();
        }

        $roles = array();

        $user = $authService->getIdentity();

        foreach ($user->getRoles() as $role) {
            $roles[] = $role->getId();
        }

        return $roles;
    }
}
