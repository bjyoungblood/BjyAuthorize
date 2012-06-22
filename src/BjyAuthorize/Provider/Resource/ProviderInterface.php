<?php

namespace BjyAuthorize\Provider\Resource;

interface ProviderInterface
{
    /**
     * @return array of \Zend\Acl\Resource\ResourceInterface
     */
    public function getResources();
}
