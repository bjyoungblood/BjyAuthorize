<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Guard;

use BjyAuthorize\Exception\UnAuthorizedException;
use Zend\EventManager\EventManagerInterface;
use Zend\Http\Request as HttpRequest;
use Zend\Mvc\MvcEvent;

/**
 * Controller Guard listener, allows checking of permissions
 * during {@see \Zend\Mvc\MvcEvent::EVENT_DISPATCH}
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 */
class Controller extends AbstractGuard
{
    /**
     * Marker for invalid route errors
     */
    const ERROR = 'error-unauthorized-controller';

    protected function extractResourcesFromRule(array $rule)
    {
        $results = array();
        $rule['action'] = isset($rule['action']) ? (array)$rule['action'] : array(null);

        foreach ((array)$rule['controller'] as $controller) {
            foreach ($rule['action'] as $action) {
                $results[] = $this->getResourceName($controller, $action);
            }
        }

        return $results;
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onDispatch'), -1000);
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
            return sprintf('controller/%s:%s', $controller, strtolower($action));
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
        $service = $this->serviceLocator->get('BjyAuthorize\Service\Authorize');
        $match = $event->getRouteMatch();
        $controller = $match->getParam('controller');
        $action = $match->getParam('action');
        $request = $event->getRequest();
        $method = $request instanceof HttpRequest ? strtolower($request->getMethod()) : null;

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
        $eventManager = $app->getEventManager();
        $eventManager->setEventPrototype($event);
        $eventManager->trigger(MvcEvent::EVENT_DISPATCH_ERROR, null, $event->getParams());
    }
}
