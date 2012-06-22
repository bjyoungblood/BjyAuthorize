<?php

namespace BjyAuthorize\Provider\Rule;

class Config implements ProviderInterface
{
    protected $rules = array();

    public function __construct(array $config = array())
    {
        $this->rules = $config;
    }

    public function getRules()
    {
        return $this->rules;
    }
}
