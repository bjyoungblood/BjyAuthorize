<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Guard;

use BjyAuthorize\Provider\Rule\ProviderInterface as RuleProviderInterface;
use BjyAuthorize\Provider\Resource\ProviderInterface as ResourceProviderInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

abstract class AbstractGuard implements GuardInterface, RuleProviderInterface, ResourceProviderInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $service;

    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * @var array[]
     */
    protected $rules = array();

    /**
     * {@inheritDoc}
     */
    public function getResources()
    {
        $resources = array();

        foreach (array_keys($this->rules) as $resource) {
            $resources[] = $resource;
        }

        return $resources;
    }

    /**
     * {@inheritDoc}
     */
    public function getRules()
    {
        $rules = array();
        foreach ($this->rules as $resource => $ruleData) {
            $rule   = array();
            $rule[] = $ruleData['roles'];
            $rule[] = $resource;

            if (isset($ruleData['assertion'])) {
                $rule[] = null; // no privilege
                $rule[] = $ruleData['assertion'];
            }

            $rules[] = $rule;
        }

        return array('allow' => $rules);
    }
}
