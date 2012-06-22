<?php

namespace BjyAuthorize\Provider\Resource;

class Config implements ProviderInterface
{
    protected $resources = array();

    public function __construct(array $config = array())
    {
        $this->resources = $config;
    }

    public function getResources()
    {
        return $this->resources;
    }
}
