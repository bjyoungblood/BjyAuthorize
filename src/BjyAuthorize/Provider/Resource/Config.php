<?php

namespace BjyAuthorize\Provider\Resource;

class Config implements ProviderInterface
{
    protected $resources = array();

    public function setOptions(array $config = array())
    {
        $this->resources = $config;
    }

    public function getResources()
    {
        return $this->resources;
    }
}
