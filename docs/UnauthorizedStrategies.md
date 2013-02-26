# View Strategies

BjyAuthorize comes with two default view strategies that are used to handle

 * `BjyAuthorize\View\UnauthorizedStrategy` (registered by default) - renders a view with an unauthorized exception message
 * `BjyAuthorize\View\RedirectionStrategy` - redirects the user to a configured route or URI

You can configure the `UnauthorizedStrategy` from the module config.

If you want to enable the `RedirectionStrategy`, simply attach it to your application's `EventManager`
at bootstrap time:


```php
namespace MyApp;

use BjyAuthorize\View\RedirectionStrategy;

class Module
{
    public function onBootstrap(EventInterface $e) {
        $application  = $e->getTarget();
        $eventManager = $application->getEventManager();

        $strategy = new RedirectionStrategy();

        // eventually set the route name (default is ZfcUser's login route)
        $strategy->setRedirectRoute('my/route/name');

        // eventually set the URI to be used for redirects
        $strategy->setRedirectUri('http://example.org/login');

        $eventManager->attach($strategy);
    }
}
```
