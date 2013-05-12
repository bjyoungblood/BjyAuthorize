<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use BjyAuthorize\Service\Authorize;
use BjyAuthorize\Service\AuthorizeAwareInterface;

/**
 * IsAllowed Controller plugin. Allows checking access to a resource/privilege in controllers.
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 */
class IsAllowed extends AbstractPlugin
{
    /**
     * @var Authorize
     */
    protected $authorizeService;

    /**
     * @param Authorize $authorizeService
     */
    public function __construct(Authorize $authorizeService)
    {
        $this->authorizeService = $authorizeService;
    }

    /**
     * @param mixed      $resource
     * @param mixed|null $privilege
     *
     * @return bool
     */
    public function __invoke($resource, $privilege = null)
    {
        return $this->authorizeService->isAllowed($resource, $privilege);
    }
}
