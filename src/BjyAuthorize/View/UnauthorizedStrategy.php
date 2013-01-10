<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\View;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Http\Response as HttpResponse;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\View\Model\ViewModel;
use BjyAuthorize\Exception\UnAuthorizedException;

/**
 * Dispatch error handler, catches exceptions related with authorization and
 * configures the application response accordingly.
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 */
class UnauthorizedStrategy implements ListenerAggregateInterface
{
    /**
     * @var string
     */
    protected $template;

    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * @param string $template name of the template to use on unauthorized requests
     */
    public function __construct($template)
    {
        $this->template = (string) $template;
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'onDispatchError'), -5000);
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
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = (string) $template;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Callback used when a dispatch error occurs. Modifies the
     * response object with an according error if the application
     * event contains an exception related with authorization.
     *
     * @param MvcEvent $event
     *
     * @return void
     */
    public function onDispatchError(MvcEvent $event)
    {
        // Do nothing if the result is a response object
        $result = $event->getResult();

        if ($result instanceof Response) {
            return;
        }

        // Common view variables
        $viewVariables = array(
           'error'      => $event->getParam('error'),
           'identity'   => $event->getParam('identity'),
        );

        switch ($event->getError()) {
            case 'error-unauthorized-controller':
                $viewVariables['controller'] = $event->getParam('controller');
                $viewVariables['action']     = $event->getParam('action');
                break;
            case 'error-unauthorized-route':
                $viewVariables['route'] = $event->getParam('route');
                break;
            case Application::ERROR_EXCEPTION:
                if (!($event->getParam('exception') instanceof UnAuthorizedException)) {
                    return;
                }

                $viewVariables['reason'] = $event->getParam('exception')->getMessage();
                $viewVariables['error']  = 'error-unauthorized';
                break;
            default:
                /*
                 * do nothing if there is no error in the event or the error
                 * does not match one of our predefined errors (we don't want
                 * our 403.phtml to handle other types of errors)
                 */

                return;
        }

        $model = new ViewModel($viewVariables);
        $model->setTemplate($this->getTemplate());
        $event->getViewModel()->addChild($model);

        $response = $event->getResponse();
        $response = $response ?: new HttpResponse();

        $response->setStatusCode(403);
        $event->setResponse($response);
    }
}
