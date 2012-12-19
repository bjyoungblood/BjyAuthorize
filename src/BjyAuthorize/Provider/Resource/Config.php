<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Provider\Resource;

/**
 * Array-based resources list
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 */
class Config implements ProviderInterface
{
    /**
     * @var \Zend\Permissions\Acl\Resource\ResourceInterface[]
     */
    protected $resources = array();

    /**
     * @param \Zend\Permissions\Acl\Resource\ResourceInterface[] $config
     */
    public function __construct(array $config = array())
    {
        $this->resources = $config;
    }

    /**
     * {@inheritDoc}
     */
    public function getResources()
    {
        return $this->resources;
    }
}
