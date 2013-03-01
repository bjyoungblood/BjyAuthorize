<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Service;

/**
 * Interface for Authorize-aware objects. Allows injection
 * of an {@see \BjyAuthorize\Service\Authorize}
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 */
interface AuthorizeAwareInterface
{
    /**
     * @param \BjyAuthorize\Service\Authorize $auth
     *
     * @return void
     */
    public function setAuthorizeService(Authorize $auth);
}
