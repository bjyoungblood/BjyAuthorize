<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Guard;

use BjyAuthorize\Exception\UnAuthorizedException;
use BjyAuthorize\Provider\Rule\ProviderInterface as RuleProviderInterface;
use BjyAuthorize\Provider\Resource\ProviderInterface as ResourceProviderInterface;

use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mvc\ApplicationInterface;
use BjyAuthorize\Service\Authorize;
use Zend\Http\Request as HttpRequest;

/**
 * Controller Guard listener, allows checking of permissions
 * during {@see \Zend\Mvc\MvcEvent::EVENT_DISPATCH}
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 */
class Controller implements GuardInterface, RuleProviderInterface, ResourceProviderInterface
{
    /**
     * Marker for invalid route errors
     */
    const ERROR = 'error-unauthorized-controller';

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var array[]
     */
    protected $rules = array();

    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * @param array                   $rules
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function __construct(array $rules, ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        foreach ($rules as $rule) {
            if (!is_array($rule['roles'])) {
                $rule['roles'] = array($rule['roles']);
            }

            $rule['action'] = isset($rule['action']) ? (array) $rule['action'] : array(null);

            foreach ((array) $rule['controller'] as $controller) {
                foreach ($rule['action'] as $action) {
                    $this->rules[$this->getResourceName($controller, $action)] = $rule['roles'];
                }
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onDispatch'), -1000);
    }

    /**
     * {@inheritDoc}
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

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
        foreach ($this->rules as $resource => $roles) {
            $rules[] = array($roles, $resource);
        }

        return array('allow' => $rules);
    }

    /**
     * Retrieves the resource name for a given controller
     *
     * @param string $controller
     * @param string $action
     *
     * @return string
     */
    public function getResourceName($controller, $action = null)
    {
        if (isset($action)) {
            return sprintf('controller/%s:%s', $controller, $action);
        }

        return sprintf('controller/%s', $controller);
    }

    /**
     * Event callback to be triggered on dispatch, causes application error triggering
     * in case of failed authorization check
     *
     * @param MvcEvent $event
     *
     * @return void
     */
    public function onDispatch(MvcEvent $event)
    {
        /* @var $service \BjyAuthorize\Service\Authorize */
        $service    = $this->serviceLocator->get('BjyAuthorize\Service\Authorize');
        $match      = $event->getRouteMatch();
        $controller = $match->getParam('controller');
        $action     = $match->getParam('action');
        $request    = $event->getRequest();
        $method     = $request instanceof HttpRequest ? strtolower($request->getMethod()) : null;

        $authorized = $service->isAllowed($this->getResourceName($controller))
            || $service->isAllowed($this->getResourceName($controller, $action))
            || ($method && $service->isAllowed($this->getResourceName($controller, $method)));

        if ($authorized) {
            return;
        }

        $event->setError(static::ERROR);
        $event->setParam('identity', $service->getIdentity());
        $event->setParam('controller', $controller);
        $event->setParam('action', $action);

        $errorMessage = sprintf("You are not authorized to access %s:%s", $controller, $action);
        $event->setParam('exception', new UnAuthorizedException($errorMessage));

        /* @var $app \Zend\Mvc\ApplicationInterface */
        $app = $event->getTarget();
        $app->getEventManager()->trigger(MvcEvent::EVENT_DISPATCH_ERROR, $event);
    }
}
