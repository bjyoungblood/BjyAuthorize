<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Guard;

use BjyAuthorize\Provider\Resource\ProviderInterface as ResourceProviderInterface;
use BjyAuthorize\Provider\Rule\ProviderInterface as RuleProviderInterface;
use Interop\Container\ContainerInterface;
use Zend\EventManager\AbstractListenerAggregate;

abstract class AbstractGuard extends AbstractListenerAggregate implements
    GuardInterface,
    RuleProviderInterface,
    ResourceProviderInterface
{
    /**
     * @var ContainerInterface
     */
    protected $serviceLocator;

    /**
     * @var array[]
     */
    protected $rules = array();

    /**
     *
     * @param array $rules
     * @param ContainerInterface $serviceLocator
     */
    public function __construct(array $rules, ContainerInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        foreach ($rules as $rule) {
            $rule['roles'] = (array)$rule['roles'];
            $rule['action'] = isset($rule['action']) ? (array)$rule['action'] : array(null);

            foreach ($this->extractResourcesFromRule($rule) as $resource) {
                $this->rules[$resource] = array('roles' => (array)$rule['roles']);

                if (isset($rule['assertion'])) {
                    $this->rules[$resource]['assertion'] = $rule['assertion'];
                }
            }
        }
    }

    abstract protected function extractResourcesFromRule(array $rule);

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
            $rule = array();
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
