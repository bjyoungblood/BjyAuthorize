<?php

namespace BjyAuthorize\Provider\Resource;

interface ProviderInterface
{
    /**
     * @return array of \Zend\Permissions\Acl\Resource\ResourceInterface
     */
    public function getResources();
}
