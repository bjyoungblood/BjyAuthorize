<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Provider\Resource;

/**
 * Resource provider interface, provides existing resources list
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 */
interface ProviderInterface
{
    /**
     * @return \Zend\Permissions\Acl\Resource\ResourceInterface[]
     */
    public function getResources();
}
